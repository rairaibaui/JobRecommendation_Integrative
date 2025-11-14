"""
Mandaluyong Business Permit AI Detector
Uses fine-tuned vision model to detect specific permit elements.
"""

import os
import torch
import torch.nn as nn
from torchvision import transforms
from PIL import Image
import numpy as np
from pathlib import Path
import json
import logging
from typing import Dict, List, Tuple, Optional

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)


class MandaluyongPermitDetector:
    """AI-powered detector for Mandaluyong business permit elements."""

    def __init__(self, model_path: str = "best_permit_detector.pth", device: str = "cpu"):
        self.device = torch.device(device)
        self.model_path = model_path
        self.model = None
        self.transform = self._create_transforms()

        # Target labels in order
        self.target_labels = [
            'has_mandaluyong_logo',
            'has_business_permit_title',
            'has_business_details',
            'has_nature_business',
            'has_business_address',
            'has_names',
            'has_issued_date',
            'has_signatures'
        ]

        self._load_model()

    def _create_transforms(self):
        """Create image preprocessing transforms."""
        return transforms.Compose([
            transforms.Resize((224, 224)),
            transforms.ToTensor(),
            transforms.Normalize(mean=[0.485, 0.456, 0.406], std=[0.229, 0.224, 0.225]),
        ])

    def _load_model(self):
        """Load the trained model."""
        try:
            # Define model architecture (same as training)
            self.model = PermitDetector(num_classes=8)
            self.model.load_state_dict(torch.load(self.model_path, map_location=self.device))
            self.model.to(self.device)
            self.model.eval()
            logger.info(f"Model loaded from {self.model_path}")
        except Exception as e:
            logger.error(f"Failed to load model: {e}")
            self.model = None

    def preprocess_image(self, image_path: str) -> Optional[torch.Tensor]:
        """Preprocess image for model input."""
        try:
            # Handle both PDF and image files
            if image_path.lower().endswith('.pdf'):
                # Convert PDF to image (first page)
                from pdf2image import convert_from_path
                images = convert_from_path(image_path, first_page=1, last_page=1)
                if not images:
                    return None
                image = images[0]
            else:
                image = Image.open(image_path).convert('RGB')

            # Apply transforms
            return self.transform(image).unsqueeze(0).to(self.device)

        except Exception as e:
            logger.error(f"Error preprocessing image {image_path}: {e}")
            return None

    def detect_elements(self, image_path: str, confidence_threshold: float = 0.5) -> Dict:
        """Detect permit elements in the image."""
        if self.model is None:
            return self._fallback_detection(image_path)

        # Preprocess image
        image_tensor = self.preprocess_image(image_path)
        if image_tensor is None:
            return self._fallback_detection(image_path)

        try:
            with torch.no_grad():
                outputs = self.model(image_tensor)
                probabilities = torch.sigmoid(outputs).cpu().numpy()[0]

            # Create results dictionary
            results = {}
            detected_elements = []

            for i, (label, prob) in enumerate(zip(self.target_labels, probabilities)):
                is_present = prob >= confidence_threshold
                results[label] = {
                    'present': bool(is_present),
                    'confidence': float(prob)
                }
                if is_present:
                    detected_elements.append(label)

            # Overall assessment
            essential_elements = [
                'has_mandaluyong_logo',
                'has_business_permit_title',
                'has_business_details',
                'has_names',
                'has_signatures'
            ]

            essential_present = all(results[element]['present'] for element in essential_elements)
            avg_confidence = np.mean([results[element]['confidence'] for element in essential_elements])

            results['summary'] = {
                'is_valid_permit': essential_present,
                'confidence_score': float(avg_confidence),
                'detected_elements': detected_elements,
                'missing_elements': [elem for elem in essential_elements if not results[elem]['present']]
            }

            return results

        except Exception as e:
            logger.error(f"Error during detection: {e}")
            return self._fallback_detection(image_path)

    def _fallback_detection(self, image_path: str) -> Dict:
        """Fallback detection when model is not available."""
        logger.warning("Using fallback detection - model not available")

        # Return uncertain results
        results = {}
        for label in self.target_labels:
            results[label] = {
                'present': False,
                'confidence': 0.0
            }

        results['summary'] = {
            'is_valid_permit': False,
            'confidence_score': 0.0,
            'detected_elements': [],
            'missing_elements': self.target_labels,
            'error': 'AI model not available'
        }

        return results

    def validate_permit(self, image_path: str) -> Dict:
        """Comprehensive permit validation."""
        detection_results = self.detect_elements(image_path)

        # Extract additional information if possible
        extracted_info = self._extract_text_info(image_path)

        return {
            'ai_detection': detection_results,
            'extracted_info': extracted_info,
            'validation_status': 'valid' if detection_results['summary']['is_valid_permit'] else 'invalid',
            'confidence': detection_results['summary']['confidence_score']
        }

    def _extract_text_info(self, image_path: str) -> Dict:
        """Extract text-based information (fallback to OCR if needed)."""
        # This could integrate with the existing OCR system
        try:
            # Try relative import first (when used as module)
            try:
                from .mandaluyong_verifier import ocr_extract_text
            except (ImportError, ValueError):
                # Fall back to absolute import (when run directly)
                import mandaluyong_verifier
                ocr_extract_text = mandaluyong_verifier.ocr_extract_text
            text = ocr_extract_text(image_path) or ""
            return {
                'raw_text_length': len(text),
                'has_text': len(text.strip()) > 0
            }
        except Exception:
            return {'error': 'OCR not available'}


class PermitDetector(nn.Module):
    """Multi-label classifier for Mandaluyong business permits."""

    def __init__(self, num_classes=8):
        super(PermitDetector, self).__init__()

        # Load EfficientNet-B0
        try:
            from torchvision.models import efficientnet_b0, EfficientNet_B0_Weights
            self.backbone = efficientnet_b0(weights=EfficientNet_B0_Weights.DEFAULT)
        except ImportError:
            # Fallback for older torchvision versions
            from torchvision.models import efficientnet_b0
            self.backbone = efficientnet_b0(pretrained=True)

        # Replace classifier head
        num_features = self.backbone.classifier[1].in_features
        self.backbone.classifier = nn.Sequential(
            nn.Dropout(0.2),
            nn.Linear(num_features, 512),
            nn.ReLU(),
            nn.Dropout(0.2),
            nn.Linear(512, num_classes),
            nn.Sigmoid()  # Multi-label classification
        )

    def forward(self, x):
        return self.backbone(x)


def convert_to_onnx(model_path: str, onnx_path: str = "permit_detector.onnx"):
    """Convert PyTorch model to ONNX for faster inference."""
    try:
        # Load model
        device = torch.device('cpu')
        model = PermitDetector(num_classes=8)
        model.load_state_dict(torch.load(model_path, map_location=device))
        model.to(device)
        model.eval()

        # Create dummy input
        dummy_input = torch.randn(1, 3, 224, 224)

        # Export to ONNX
        torch.onnx.export(
            model,
            dummy_input,
            onnx_path,
            input_names=['input'],
            output_names=['output'],
            dynamic_axes={'input': {0: 'batch_size'}, 'output': {0: 'batch_size'}},
            opset_version=11
        )

        logger.info(f"Model converted to ONNX: {onnx_path}")
        return True

    except Exception as e:
        logger.error(f"Failed to convert to ONNX: {e}")
        return False


# Global detector instance
_detector = None

def get_detector(model_path: str = "best_permit_detector.pth") -> MandaluyongPermitDetector:
    """Get or create detector instance."""
    global _detector
    if _detector is None:
        _detector = MandaluyongPermitDetector(model_path)
    return _detector


def validate_document(file_path: str) -> Dict:
    """Main validation function for external use."""
    detector = get_detector()
    return detector.validate_permit(file_path)


if __name__ == "__main__":
    # Test the detector
    detector = MandaluyongPermitDetector()

    # Test with a sample image if available
    test_files = list(Path("training_data").glob("*.png"))[:5]

    for test_file in test_files:
        print(f"\nTesting {test_file}")
        results = detector.validate_permit(str(test_file))
        print(f"Valid: {results['validation_status']}")
        print(f"Confidence: {results['confidence']:.3f}")
        print(f"Detected: {results['ai_detection']['summary']['detected_elements']}")
