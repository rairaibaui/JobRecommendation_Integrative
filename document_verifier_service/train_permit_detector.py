"""
Training script for Mandaluyong business permit detection AI model.
Fine-tunes EfficientNet-B0 for multi-label classification of permit elements.
"""

import os
import json
import torch
import torch.nn as nn
import torch.optim as optim
from torch.utils.data import Dataset, DataLoader
from torchvision import transforms
from PIL import Image
import numpy as np
from pathlib import Path
from sklearn.metrics import classification_report, accuracy_score
from sklearn.model_selection import train_test_split
import pandas as pd
from tqdm import tqdm
import matplotlib.pyplot as plt
import warnings
warnings.filterwarnings('ignore')


class PermitDataset(Dataset):
    """Dataset for Mandaluyong business permit detection."""

    def __init__(self, data_dir, labels_file, transform=None, is_train=True):
        self.data_dir = Path(data_dir)
        self.transform = transform
        self.is_train = is_train

        # Load labels
        with open(labels_file, 'r') as f:
            self.labels_data = json.load(f)

        # Define target labels for multi-label classification
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

        # Filter out any invalid entries
        self.valid_entries = []
        for item in self.labels_data:
            img_path = Path(item['image_path'])
            if img_path.exists():
                self.valid_entries.append(item)

        print(f"Loaded {len(self.valid_entries)} valid samples")

    def __len__(self):
        return len(self.valid_entries)

    def __getitem__(self, idx):
        item = self.valid_entries[idx]
        img_path = Path(item['image_path'])

        # Load image
        try:
            image = Image.open(img_path).convert('RGB')
        except Exception as e:
            print(f"Error loading {img_path}: {e}")
            # Return a blank image as fallback
            image = Image.new('RGB', (224, 224), (255, 255, 255))

        # Extract labels
        labels = []
        for label_name in self.target_labels:
            # Convert to binary (1 if present, 0 if not)
            value = item.get(label_name, False)
            if isinstance(value, bool):
                labels.append(1 if value else 0)
            else:
                labels.append(1 if value else 0)  # Handle None/null as 0

        labels = torch.tensor(labels, dtype=torch.float32)

        if self.transform:
            image = self.transform(image)

        return image, labels


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


def create_data_transforms():
    """Create data transforms for training and validation."""
    train_transform = transforms.Compose([
        transforms.Resize((224, 224)),
        transforms.RandomHorizontalFlip(p=0.3),
        transforms.RandomRotation(degrees=5),
        transforms.ColorJitter(brightness=0.2, contrast=0.2, saturation=0.2),
        transforms.ToTensor(),
        transforms.Normalize(mean=[0.485, 0.456, 0.406], std=[0.229, 0.224, 0.225]),
    ])

    val_transform = transforms.Compose([
        transforms.Resize((224, 224)),
        transforms.ToTensor(),
        transforms.Normalize(mean=[0.485, 0.456, 0.406], std=[0.229, 0.224, 0.225]),
    ])

    return train_transform, val_transform


def train_model(model, train_loader, val_loader, num_epochs=20, device='cpu'):
    """Train the permit detection model."""
    criterion = nn.BCELoss()  # Binary Cross Entropy for multi-label
    optimizer = optim.Adam(model.parameters(), lr=1e-4, weight_decay=1e-4)
    scheduler = optim.lr_scheduler.ReduceLROnPlateau(optimizer, mode='min', patience=3, factor=0.5)

    best_val_loss = float('inf')
    patience = 7
    patience_counter = 0

    for epoch in range(num_epochs):
        # Training phase
        model.train()
        train_loss = 0.0
        train_preds = []
        train_labels = []

        for images, labels in tqdm(train_loader, desc=f'Epoch {epoch+1}/{num_epochs} - Training'):
            images, labels = images.to(device), labels.to(device)

            optimizer.zero_grad()
            outputs = model(images)
            loss = criterion(outputs, labels)
            loss.backward()
            optimizer.step()

            train_loss += loss.item()
            train_preds.extend(outputs.cpu().detach().numpy())
            train_labels.extend(labels.cpu().numpy())

        train_loss /= len(train_loader)

        # Validation phase
        model.eval()
        val_loss = 0.0
        val_preds = []
        val_labels = []

        with torch.no_grad():
            for images, labels in tqdm(val_loader, desc=f'Epoch {epoch+1}/{num_epochs} - Validation'):
                images, labels = images.to(device), labels.to(device)
                outputs = model(images)
                loss = criterion(outputs, labels)

                val_loss += loss.item()
                val_preds.extend(outputs.cpu().numpy())
                val_labels.extend(labels.cpu().numpy())

        val_loss /= len(val_loader)

        # Calculate metrics
        val_preds_binary = (np.array(val_preds) > 0.5).astype(int)
        val_labels = np.array(val_labels)

        accuracy = accuracy_score(val_labels.flatten(), val_preds_binary.flatten())

        print(f"Epoch {epoch+1}/{num_epochs}")
        print(f"Train Loss: {train_loss:.4f}")
        print(f"Val Loss: {val_loss:.4f}")
        print(f"Val Accuracy: {accuracy:.4f}")

        # Save best model
        if val_loss < best_val_loss:
            best_val_loss = val_loss
            torch.save(model.state_dict(), 'best_permit_detector.pth')
            patience_counter = 0
            print("Saved best model!")
        else:
            patience_counter += 1

        scheduler.step(val_loss)

        # Early stopping
        if patience_counter >= patience:
            print(f"Early stopping at epoch {epoch+1}")
            break

    return model


def evaluate_model(model, test_loader, device='cpu'):
    """Evaluate the trained model."""
    model.eval()
    all_preds = []
    all_labels = []

    with torch.no_grad():
        for images, labels in tqdm(test_loader, desc='Evaluating'):
            images, labels = images.to(device), labels.to(device)
            outputs = model(images)

            all_preds.extend(outputs.cpu().numpy())
            all_labels.extend(labels.cpu().numpy())

    # Convert to binary predictions
    preds_binary = (np.array(all_preds) > 0.5).astype(int)
    labels = np.array(all_labels)

    # Calculate per-class metrics
    target_names = [
        'Mandaluyong Logo',
        'Business Permit Title',
        'Business Details',
        'Nature of Business',
        'Business Address',
        'Names',
        'Issued Date',
        'Signatures'
    ]

    print("\nClassification Report:")
    print(classification_report(labels, preds_binary, target_names=target_names))

    return preds_binary, labels


def main():
    """Main training function."""
    # Set device
    device = torch.device('cuda' if torch.cuda.is_available() else 'cpu')
    print(f"Using device: {device}")

    # Data paths
    data_dir = 'training_data'
    labels_file = os.path.join(data_dir, 'labels.json')

    if not os.path.exists(labels_file):
        print(f"Labels file not found: {labels_file}")
        return

    # Create datasets
    train_transform, val_transform = create_data_transforms()

    # Split data
    with open(labels_file, 'r') as f:
        all_data = json.load(f)

    train_data, temp_data = train_test_split(all_data, test_size=0.3, random_state=42)
    val_data, test_data = train_test_split(temp_data, test_size=0.5, random_state=42)

    # Save split data for reproducibility
    with open('train_labels.json', 'w') as f:
        json.dump(train_data, f)
    with open('val_labels.json', 'w') as f:
        json.dump(val_data, f)
    with open('test_labels.json', 'w') as f:
        json.dump(test_data, f)

    # Create datasets
    train_dataset = PermitDataset(data_dir, 'train_labels.json', transform=train_transform, is_train=True)
    val_dataset = PermitDataset(data_dir, 'val_labels.json', transform=val_transform, is_train=False)
    test_dataset = PermitDataset(data_dir, 'test_labels.json', transform=val_transform, is_train=False)

    # Create data loaders
    batch_size = 16  # Adjust based on available RAM
    train_loader = DataLoader(train_dataset, batch_size=batch_size, shuffle=True, num_workers=0)
    val_loader = DataLoader(val_dataset, batch_size=batch_size, shuffle=False, num_workers=0)
    test_loader = DataLoader(test_dataset, batch_size=batch_size, shuffle=False, num_workers=0)

    print(f"Train samples: {len(train_dataset)}")
    print(f"Validation samples: {len(val_dataset)}")
    print(f"Test samples: {len(test_dataset)}")

    # Create model
    model = PermitDetector(num_classes=8)
    model = model.to(device)

    # Train model
    print("Starting training...")
    trained_model = train_model(model, train_loader, val_loader, num_epochs=20, device=device)

    # Load best model for evaluation
    trained_model.load_state_dict(torch.load('best_permit_detector.pth'))

    # Evaluate on test set
    print("Evaluating on test set...")
    evaluate_model(trained_model, test_loader, device=device)

    print("Training completed! Best model saved as 'best_permit_detector.pth'")


if __name__ == "__main__":
    main()
