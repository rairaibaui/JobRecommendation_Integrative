"""
Prepare training data from a single PDF document.
Converts PDF to images and creates augmented versions for training.
"""

import os
import json
from pathlib import Path
from PIL import Image, ImageEnhance, ImageFilter
import numpy as np
from pdf2image import convert_from_path
import random


def convert_pdf_to_images(pdf_path: str, output_dir: str = "training_data") -> list:
    """Convert PDF pages to images."""
    output_path = Path(output_dir)
    output_path.mkdir(exist_ok=True)
    
    print(f"Converting PDF to images: {pdf_path}")
    try:
        images = convert_from_path(pdf_path, dpi=200)
        image_paths = []
        
        for i, image in enumerate(images):
            # Convert to RGB if needed
            if image.mode != 'RGB':
                image = image.convert('RGB')
            
            # Save each page
            img_filename = f"business_locational_clearance_page_{i+1}.png"
            img_path = output_path / img_filename
            image.save(img_path, 'PNG')
            image_paths.append(str(img_path))
            print(f"Saved page {i+1}: {img_path}")
        
        return image_paths
    except Exception as e:
        print(f"Error converting PDF: {e}")
        return []


def augment_image(image: Image.Image, augmentation_type: str) -> Image.Image:
    """Apply data augmentation to an image."""
    if augmentation_type == 'original':
        return image
    
    elif augmentation_type == 'brightness_up':
        enhancer = ImageEnhance.Brightness(image)
        return enhancer.enhance(1.2)
    
    elif augmentation_type == 'brightness_down':
        enhancer = ImageEnhance.Brightness(image)
        return enhancer.enhance(0.8)
    
    elif augmentation_type == 'contrast_up':
        enhancer = ImageEnhance.Contrast(image)
        return enhancer.enhance(1.2)
    
    elif augmentation_type == 'contrast_down':
        enhancer = ImageEnhance.Contrast(image)
        return enhancer.enhance(0.8)
    
    elif augmentation_type == 'rotate_left':
        return image.rotate(2, expand=True, fillcolor='white')
    
    elif augmentation_type == 'rotate_right':
        return image.rotate(-2, expand=True, fillcolor='white')
    
    elif augmentation_type == 'slight_blur':
        return image.filter(ImageFilter.GaussianBlur(radius=0.5))
    
    elif augmentation_type == 'sharpness':
        enhancer = ImageEnhance.Sharpness(image)
        return enhancer.enhance(1.2)
    
    elif augmentation_type == 'color_saturation':
        enhancer = ImageEnhance.Color(image)
        return enhancer.enhance(1.1)
    
    else:
        return image


def generate_augmented_dataset(base_image_path: str, num_augmentations: int = 100, output_dir: str = "training_data") -> list:
    """Generate augmented versions of the base image."""
    output_path = Path(output_dir)
    output_path.mkdir(exist_ok=True)
    
    # Load base image
    base_image = Image.open(base_image_path).convert('RGB')
    base_name = Path(base_image_path).stem
    
    augmentation_types = [
        'original',
        'brightness_up',
        'brightness_down',
        'contrast_up',
        'contrast_down',
        'rotate_left',
        'rotate_right',
        'slight_blur',
        'sharpness',
        'color_saturation'
    ]
    
    dataset = []
    
    # Always include original
    original_path = output_path / f"{base_name}_aug_000.png"
    base_image.save(original_path, 'PNG')
    dataset.append({
        'image_path': str(original_path),
        'augmentation': 'original'
    })
    
    # Generate augmented versions
    for i in range(1, num_augmentations):
        aug_type = random.choice(augmentation_types)
        augmented = augment_image(base_image, aug_type)
        
        # Save augmented image
        aug_filename = f"{base_name}_aug_{i:03d}.png"
        aug_path = output_path / aug_filename
        augmented.save(aug_path, 'PNG')
        
        dataset.append({
            'image_path': str(aug_path),
            'augmentation': aug_type
        })
    
    print(f"Generated {len(dataset)} augmented images")
    return dataset


def create_labels_for_clearance(image_paths: list) -> list:
    """Create labels for business locational clearance documents."""
    labels = []
    
    # Business Locational Clearance typically has:
    # - Mandaluyong logo (usually present)
    # - Business permit/clearance title (present)
    # - Business details (present)
    # - Nature of business (may or may not be present in clearance)
    # - Business address (present)
    # - Names (present - owner/proprietor)
    # - Issued date (present)
    # - Signatures (present)
    
    for idx, img_path in enumerate(image_paths):
        label_entry = {
            'id': idx,
            'image_path': img_path,
            'has_mandaluyong_logo': True,  # Business clearance typically has logo
            'has_business_permit_title': True,  # "Business Locational Clearance" is the title
            'has_business_details': True,  # Contains business information
            'has_nature_business': False,  # Clearance may not always have this
            'has_business_address': True,  # Address is required
            'has_names': True,  # Owner/proprietor name present
            'has_issued_date': True,  # Date of issuance present
            'has_signatures': True,  # Official signatures present
            'document_type': 'barangay',  # Business Locational Clearance is typically from barangay
            'business_name': None,  # Will be extracted during verification
            'owner_name': None,  # Will be extracted during verification
            'nature_business': None,  # May not be present
            'business_address': None,  # Will be extracted during verification
            'issued_date': None,  # Will be extracted during verification
            'valid_until': None,  # May not be present in clearance
            'signature_official': None  # Will be extracted during verification
        }
        labels.append(label_entry)
    
    return labels


def main():
    """Main function to prepare training data from single PDF."""
    # Paths
    pdf_path = Path("training_data/better business locational clearance.pdf")
    output_dir = "training_data"
    
    if not pdf_path.exists():
        print(f"Error: PDF file not found at {pdf_path}")
        return
    
    # Step 1: Convert PDF to images
    print("Step 1: Converting PDF to images...")
    image_paths = convert_pdf_to_images(str(pdf_path), output_dir)
    
    if not image_paths:
        print("Error: Failed to convert PDF to images")
        return
    
    # Step 2: Generate augmented versions from first page (or all pages)
    print("\nStep 2: Generating augmented training images...")
    all_augmented_paths = []
    
    # Use the first page as base for augmentation (or use all pages)
    # For now, we'll use the first page and generate many augmentations
    base_image_path = image_paths[0]
    augmented_dataset = generate_augmented_dataset(
        base_image_path, 
        num_augmentations=500,  # Generate 500 augmented versions
        output_dir=output_dir
    )
    
    all_augmented_paths = [item['image_path'] for item in augmented_dataset]
    
    # Step 3: Create labels.json
    print("\nStep 3: Creating labels.json...")
    labels = create_labels_for_clearance(all_augmented_paths)
    
    labels_file = Path(output_dir) / "labels.json"
    with open(labels_file, 'w') as f:
        json.dump(labels, f, indent=2)
    
    print(f"\n✓ Successfully created training dataset!")
    print(f"  - Base images: {len(image_paths)}")
    print(f"  - Augmented images: {len(all_augmented_paths)}")
    print(f"  - Total training samples: {len(labels)}")
    print(f"  - Labels file: {labels_file}")
    
    # Clean up: Optionally remove old synthetic permit images
    print("\nNote: Old synthetic permit images (permit_*.png) are still in the directory.")
    print("You may want to remove them to avoid confusion.")


if __name__ == "__main__":
    main()

