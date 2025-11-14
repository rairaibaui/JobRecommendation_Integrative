"""
Data collection and synthetic data generation for Mandaluyong business permit AI training.
"""

import os
import json
import random
from pathlib import Path
from PIL import Image, ImageDraw, ImageFont
import numpy as np
from typing import List, Dict, Tuple


class MandaluyongPermitGenerator:
    """Generate synthetic Mandaluyong business permit images for training data."""

    def __init__(self, output_dir: str = "training_data"):
        self.output_dir = Path(output_dir)
        self.output_dir.mkdir(exist_ok=True)

        # Template dimensions
        self.width = 800
        self.height = 1100

        # Common permit elements
        self.templates = {
            'dti': {
                'header': 'Republic of the Philippines\nDepartment of Trade and Industry\nMandaluyong City',
                'title': 'Certificate of Business Name Registration',
                'elements': ['business_name', 'owner_name', 'nature_business', 'business_address', 'issued_date', 'valid_until', 'signature']
            },
            'barangay': {
                'header': 'Republic of the Philippines\nOffice of the Punong Barangay\nMandaluyong City',
                'title': 'Business Locational Clearance',
                'elements': ['business_name', 'owner_name', 'business_address', 'issued_date', 'valid_until', 'signature']
            },
            'mayor': {
                'header': 'Republic of the Philippines\nCity Government of Mandaluyong\nOffice of the Mayor',
                'title': 'Business Permit',
                'elements': ['business_name', 'owner_name', 'nature_business', 'business_address', 'issued_date', 'valid_until', 'signature']
            }
        }

        # Sample data for generation
        self.business_names = [
            'MARGARITA SARI-SARI STORE',
            'JUAN\'S HARDWARE SHOP',
            'MARIA\'S BEAUTY SALON',
            'PEDRO\'S CARINDERYA',
            'ANA\'S BAKERY',
            'CARLOS\'S LAUNDRY SERVICE',
            'TERESA\'S BOOKSTORE',
            'MIGUEL\'S PHOTOGRAPHY STUDIO',
            'ROSARIO\'S TAILOR SHOP',
            'ANTONIO\'S GROCERY STORE'
        ]

        self.owner_names = [
            'MARGARITA PALLES MONDERO',
            'JUAN DELA CRUZ',
            'MARIA SANTOS REYES',
            'PEDRO GARCIA LOPEZ',
            'ANA MARIA RODRIGUEZ',
            'CARLOS MANUEL SANTIAGO',
            'TERESA CRUZ VILLANUEVA',
            'MIGUEL ANGELES TORRES',
            'ROSARIO LIM YAP',
            'ANTONIO RAMOS GONZALES'
        ]

        self.business_natures = [
            'Retail Trade',
            'Food Service',
            'Personal Care Services',
            'General Services',
            'Wholesale Trade',
            'Manufacturing',
            'Transportation Services',
            'Professional Services'
        ]

        self.addresses = [
            '123 Rizal Street, Barangay Addition Hills, Mandaluyong City',
            '456 Shaw Boulevard, Barangay Pleasant Hills, Mandaluyong City',
            '789 EDSA, Barangay Highway Hills, Mandaluyong City',
            '321 Boni Avenue, Barangay Plainview, Mandaluyong City',
            '654 Calbayog Street, Barangay Hagdang Bato, Mandaluyong City'
        ]

        self.official_names = [
            'MA. CRISTINA A. ROQUE',
            'CARLITO T. CERNAD',
            'HON. CAESAR G. PELAEZ',
            'ATTY. MARIA ELENA S. CRUZ',
            'DIR. ANTONIO B. SANTOS'
        ]

    def generate_permit_image(self, template_type: str, include_logo: bool = True) -> Tuple[Image.Image, Dict]:
        """Generate a synthetic permit image with labels."""
        if template_type not in self.templates:
            raise ValueError(f"Unknown template type: {template_type}")

        template = self.templates[template_type]

        # Create white background
        img = Image.new('RGB', (self.width, self.height), 'white')
        draw = ImageDraw.Draw(img)

        # Try to use a default font, fallback to basic
        try:
            font_title = ImageFont.truetype("arial.ttf", 24)
            font_header = ImageFont.truetype("arial.ttf", 16)
            font_body = ImageFont.truetype("arial.ttf", 14)
        except:
            font_title = ImageFont.load_default()
            font_header = ImageFont.load_default()
            font_body = ImageFont.load_default()

        y_pos = 50

        # Header
        for line in template['header'].split('\n'):
            draw.text((50, y_pos), line, fill='black', font=font_header)
            y_pos += 25

        y_pos += 20

        # Mandaluyong City Logo placeholder (if included)
        if include_logo:
            # Draw a simple circular logo
            logo_center = (self.width // 2, y_pos + 30)
            draw.ellipse([logo_center[0]-25, logo_center[1]-25, logo_center[0]+25, logo_center[1]+25], fill='blue')
            draw.text((logo_center[0]-20, logo_center[1]-10), 'MC', fill='white', font=font_title)
            y_pos += 70

        # Title
        draw.text((50, y_pos), template['title'], fill='black', font=font_title)
        y_pos += 50

        # Generate random data
        business_name = random.choice(self.business_names)
        owner_name = random.choice(self.owner_names)
        nature = random.choice(self.business_natures) if 'nature_business' in template['elements'] else None
        address = random.choice(self.addresses)
        issued_date = '2024-11-01'
        valid_until = '2026-11-01'
        official = random.choice(self.official_names)

        # Business details
        details = [
            f"Business Name: {business_name}",
            f"Owner/Proprietor: {owner_name}",
        ]

        if nature:
            details.append(f"Nature of Business: {nature}")

        details.extend([
            f"Business Address: {address}",
            f"Date Issued: {issued_date}",
            f"Valid Until: {valid_until}",
            f"Signature: {official}"
        ])

        for detail in details:
            draw.text((50, y_pos), detail, fill='black', font=font_body)
            y_pos += 25

        # Labels for training
        labels = {
            'has_mandaluyong_logo': include_logo,
            'has_business_permit_title': True,
            'has_business_details': True,
            'has_nature_business': nature is not None,
            'has_business_address': True,
            'has_names': True,
            'has_issued_date': True,
            'has_signatures': True,
            'document_type': template_type,
            'business_name': business_name,
            'owner_name': owner_name,
            'nature_business': nature,
            'business_address': address,
            'issued_date': issued_date,
            'valid_until': valid_until,
            'signature_official': official
        }

        return img, labels

    def generate_dataset(self, num_samples: int = 500) -> List[Dict]:
        """Generate a complete dataset of synthetic permits."""
        dataset = []

        for i in range(num_samples):
            # Randomly choose template type
            template_type = random.choice(list(self.templates.keys()))

            # Sometimes exclude logo to create negative examples
            include_logo = random.random() > 0.1  # 90% have logo

            img, labels = self.generate_permit_image(template_type, include_logo)

            # Save image
            img_filename = f"permit_{i:04d}.png"
            img_path = self.output_dir / img_filename
            img.save(img_path)

            # Add to dataset
            labels['image_path'] = str(img_path)
            labels['id'] = i
            dataset.append(labels)

        # Save labels to JSON
        labels_path = self.output_dir / "labels.json"
        with open(labels_path, 'w') as f:
            json.dump(dataset, f, indent=2)

        return dataset

    def add_noise_and_variations(self, img: Image.Image) -> Image.Image:
        """Add realistic noise and variations to make training more robust."""
        # Convert to numpy array
        img_array = np.array(img)

        # Add slight rotation
        if random.random() > 0.7:
            angle = random.uniform(-2, 2)
            img = img.rotate(angle, expand=True, fillcolor='white')

        # Add slight blur or noise
        if random.random() > 0.8:
            # Simple noise addition
            noise = np.random.normal(0, 5, img_array.shape).astype(np.uint8)
            img_array = np.clip(img_array + noise, 0, 255)
            img = Image.fromarray(img_array)

        return img


def collect_real_samples(source_dir: str, output_dir: str = "training_data"):
    """Collect and label real permit samples if available."""
    source_path = Path(source_dir)
    output_path = Path(output_dir)
    output_path.mkdir(exist_ok=True)

    real_samples = []

    # Look for existing permit files
    for ext in ['*.pdf', '*.jpg', '*.jpeg', '*.png']:
        for file_path in source_path.glob(ext):
            # For now, create placeholder labels
            # In practice, you'd manually label these
            labels = {
                'image_path': str(file_path),
                'is_real_sample': True,
                'needs_manual_labeling': True
            }
            real_samples.append(labels)

    if real_samples:
        real_labels_path = output_path / "real_samples_labels.json"
        with open(real_labels_path, 'w') as f:
            json.dump(real_samples, f, indent=2)

    return real_samples


if __name__ == "__main__":
    generator = MandaluyongPermitGenerator()

    print("Generating synthetic training data...")
    dataset = generator.generate_dataset(500)

    print(f"Generated {len(dataset)} synthetic samples")
    print(f"Saved to {generator.output_dir}")

    # Example of collecting real samples (uncomment when you have real data)
    # real_samples = collect_real_samples("path/to/real/permits")
    # print(f"Found {len(real_samples)} real samples")
