# Philippine Business Permit Validation Reference

## Valid Business Permit Examples

Based on actual documents from Mandaluyong City, Philippines, here are the characteristics of **legitimate business permits** that the AI should **APPROVE**:

---

## Example 1: Barangay Business Locational Clearance

### Document Details
- **Issuer:** Office of the Punong Barangay, Barangay Addition Hills, Mandaluyong City
- **Type:** Business Locational Clearance
- **Date:** May 20, 2025

### Key Validation Points ✅

**Official Headers:**
- "Republic of the Philippines"
- "Mandaluyong City"
- "Office of the Punong Barangay"
- "Barangay Addition Hills"

**Official Seals/Logos:**
- Barangay seal (circular seal with imagery)
- Official letterhead design
- Government formatting

**Official Information:**
- Lists all Barangay officials (Sangguniang Barangay members)
- Chairman Committee listings
- Multiple official names

**Business Details:**
- Business owner: MARGARITA P. MONDERO
- Business name: MARGIE STORE
- Firm name: SARI-SARI STORE
- Complete address: "PH. 3 LOT 12 BLK E. BLK 40 Brgy. Addition Hills, Mandaluyong City"
- Nature of business: SARI-SARI STORE

**Signature & Authentication:**
- Signed by: CARLITO H. CARNAL (Punpong Barangay)
- For: MS. JESSA MAE G. NATAD, MPA (Barangay Secretary)
- Official signature present
- Note: "Not Valid w/o O.R & Seal"

**Security Features:**
- Official seal/stamp visible
- Proper government formatting
- Checkboxes for business type (Sole Proprietor/Partnership/Corporation)

---

## Example 2: DTI Certificate of Business Name Registration

### Document Details
- **Issuer:** Department of Trade and Industry (DTI)
- **Type:** Certificate of Business Name Registration
- **Date:** February 21, 2025
- **Validity:** 5 years (until February 21, 2030)

### Key Validation Points ✅

**Official Headers:**
- DTI official logo (top left)
- "This certifies that"
- Official DTI formatting

**Business Information:**
- Business name: MARGARITA SARI-SARI STORE (BARANGAY)
- Location: ADDITION HILLS, CITY OF MANDALUYONG NCR - NATIONAL CAPITAL REGION
- Registered under: Act 3883, as amended by Act 4147 and Republic Act No. 883
- Owner: MARGARITA PALLES MONDERO

**Registration Details:**
- Business Name No.: **6940949**
- Valid from: February 21, 2025
- Valid to: February 21, 2030
- Reference No.: **RNHH377617398411**

**Official Signature:**
- Signed by: MA. CRISTINA A. ROQUE
- Title: Secretary
- Date: February 21, 2025

**Security Features:**
- **QR Code** (for digital verification)
- Documentary Stamp Tax Paid: Php 30.00
- Official watermark/design
- Government-issued formatting

**Legal Notice:**
- "This certificate is not a license to engage in any kind of business and valid only at the scope indicated herein"
- Compliance statement with laws and regulations

---

## AI Validation Criteria (Updated for Philippine Context)

### ✅ What to Look For (APPROVE if present):

**1. Philippine Government Agencies:**
- DTI (Department of Trade and Industry)
- SEC (Securities and Exchange Commission)
- Barangay Office
- City/Municipal Hall
- Mayor's Office
- BIR (Bureau of Internal Revenue)

**2. Official Document Types:**
- DTI Certificate of Business Name Registration
- SEC Certificate of Registration/Incorporation
- Barangay Business Clearance
- Barangay Locational Clearance
- Mayor's Permit / Business Permit
- BIR Certificate of Registration (COR)

**3. Security Features:**
- Official government logos (DTI, SEC, Barangay seal, City seal)
- QR codes (especially on DTI certificates)
- Registration numbers / Business Name Numbers
- Reference numbers (format: RNHH + numbers)
- Official signatures with titles
- Stamps and seals
- Documentary tax stamps

**4. Required Information:**
- Business name
- Business owner/proprietor name
- Business address (Barangay, City, Region)
- Nature of business
- Validity dates (from/to)
- Issuance date
- Registration/permit number

**5. Philippine-Specific Indicators:**
- NCR (National Capital Region) or other regions
- Barangay name
- City/Municipality (e.g., Mandaluyong City, Quezon City, Manila)
- Philippine address format
- "Republic of the Philippines" header
- Philippine government official titles (Barangay Secretary, Punong Barangay, DTI Secretary)

### ❌ Red Flags (REJECT if present):

**1. Missing Critical Elements:**
- No official seal or logo
- No registration number
- No signature
- No validity dates
- No government agency name

**2. Obvious Fakes:**
- Personal photos
- Screenshots
- Blank documents
- Random receipts (not government-issued)
- Printed text without official formatting
- No security features

**3. Expired/Invalid:**
- Validity date has passed
- Very old permits (>2 years old)
- Revoked or cancelled stamps

**4. Suspicious Indicators:**
- Blurry or low quality (intentionally unclear)
- Obvious photo editing artifacts
- Mismatched information
- Incorrect government agency names
- Wrong document format

---

## Common Valid Combinations

Philippine businesses typically need **multiple documents**. Any of these combinations are valid:

### For Sole Proprietorship (Sari-sari Store, Small Business):
- ✅ DTI Business Name Registration **+** Barangay Clearance
- ✅ DTI Business Name Registration **+** Mayor's Permit
- ✅ Barangay Clearance **+** BIR COR
- ✅ Mayor's Permit alone (if complete)

### For Corporations:
- ✅ SEC Certificate of Registration/Incorporation
- ✅ SEC Certificate **+** Mayor's Permit
- ✅ SEC Certificate **+** Barangay Clearance

### Acceptable Single Documents:
- ✅ DTI Certificate (if recent and complete)
- ✅ SEC Certificate (for corporations)
- ✅ Mayor's Permit (if official and recent)
- ✅ Comprehensive Barangay Business Clearance (with all details)

---

## Sample AI Decision Matrix

Based on the reference documents:

| Document Type | Has Seal? | Has Reg# | Valid Date? | Owner Matches? | **Decision** |
|--------------|-----------|----------|-------------|----------------|--------------|
| DTI Cert + QR Code | ✅ | ✅ (6940949) | ✅ (2025-2030) | ✅ | **APPROVE** ✅ |
| Barangay Clearance | ✅ | ⚠️ (has O.R req) | ✅ (2025) | ✅ | **APPROVE** ✅ |
| Random photo | ❌ | ❌ | ❌ | ❌ | **REJECT** ❌ |
| Blurry permit | ⚠️ | ⚠️ | ⚠️ | ⚠️ | **MANUAL REVIEW** ⚠️ |

---

## Expected AI Confidence Scores

Based on these reference documents:

### High Confidence (≥85%) - Auto-Approve ✅
- DTI Certificate with QR code, clear text, valid dates
- SEC Certificate with all official elements
- Mayor's Permit with city seal and complete info
- Barangay Clearance with official seal and signatures

**Example:**
```json
{
  "is_business_permit": true,
  "confidence_score": 95,
  "document_type": "DTI Certificate of Business Name Registration",
  "has_official_seals": true,
  "has_registration_number": true,
  "business_name_matches": true,
  "appears_authentic": true,
  "is_expired": false,
  "issuing_authority": "Department of Trade and Industry (DTI)",
  "validity_dates": "February 21, 2025 to February 21, 2030",
  "recommendation": "APPROVE"
}
```

### Medium Confidence (50-84%) - Manual Review ⚠️
- Partially visible document
- Unclear image quality
- Missing some security features but appears legitimate
- Unconventional but possibly valid document

### Low Confidence (<50%) - Auto-Reject ❌
- No official seals
- No registration numbers
- Personal photos
- Random documents
- Obviously fake

---

## Testing Recommendations

### Test Cases Using These Examples

**1. Test with DTI Certificate:**
```
Expected Result: APPROVE
Confidence: 90-95%
Reason: Official DTI document with QR code, registration number, valid dates
```

**2. Test with Barangay Clearance:**
```
Expected Result: APPROVE
Confidence: 85-92%
Reason: Official barangay document with seal, signatures, complete info
```

**3. Test with Both Documents:**
```
Expected Result: APPROVE
Confidence: 95-98%
Reason: Multiple valid documents, comprehensive verification
```

**4. Test with Personal Photo:**
```
Expected Result: REJECT
Confidence: 5-15%
Reason: Not a business document, no official elements
```

---

## AI Prompt Enhancement

Based on these samples, the AI now recognizes:

✅ **Philippine-specific documents:**
- DTI certificates with QR codes
- Barangay clearances with official seals
- Philippine address formats
- Filipino government official titles

✅ **Security features:**
- QR codes on DTI certificates
- Barangay seals and stamps
- Documentary tax stamps
- Reference number formats (RNHH...)

✅ **Validity indicators:**
- 5-year validity for DTI (2025-2030)
- Recent issuance dates
- Proper date formats
- Expiration checking

✅ **Business types:**
- Sole proprietorship (DTI)
- Corporations (SEC)
- Sari-sari stores (common small business)
- Barangay-level businesses

---

## Admin Review Guidelines

For documents flagged as **MANUAL REVIEW**:

### Check These Elements:

1. **Document Type:**
   - Is it a recognized Philippine business document?
   - DTI, SEC, Barangay, Mayor's Permit?

2. **Issuing Authority:**
   - Valid government agency?
   - Correct spelling of agency name?
   - Proper logo/seal?

3. **Registration Numbers:**
   - Business Name No. for DTI (numeric)
   - SEC Registration No.
   - Reference numbers (RNHH format for DTI)

4. **Dates:**
   - Issuance date recent?
   - Validity period reasonable? (DTI: 5 years, others vary)
   - Not expired?

5. **Business Information:**
   - Owner name matches or is related?
   - Business name reasonable?
   - Philippine address?

6. **Security Features:**
   - QR code present (DTI)?
   - Official stamps/seals?
   - Signatures?
   - Documentary tax stamp?

### Decision Making:

**Approve if:**
- Clearly a government-issued business document
- All critical elements present
- No obvious signs of tampering
- Matches business owner information

**Reject if:**
- Not a business document
- Missing critical elements
- Obviously fake or altered
- Expired or invalid

**Request More Info if:**
- Unclear image quality
- Partial document visible
- Unusual but possibly valid format

---

## Version History

**v2.1** - November 3, 2025
- Added Philippine-specific validation criteria
- Included DTI certificate recognition
- Added Barangay clearance validation
- Enhanced QR code detection
- Updated acceptable document types

---

## References

Based on actual Philippine business permits:
- DTI Certificate of Business Name Registration (2025-2030)
- Barangay Business Locational Clearance (Mandaluyong City)
- Republic Act No. 883 (Business Name Registration)
- Act 3883 and Act 4147 (Business Registration Acts)
