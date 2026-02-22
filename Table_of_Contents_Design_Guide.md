# Table of Contents Design Guide for Microsoft Word

## Design Option 1: Modern Professional Style

### Visual Layout:
```
┌─────────────────────────────────────────────────────────────┐
│                    TABLE OF CONTENTS                        │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  1. Introduction ................................................ 3  │
│  2. Project Requirements ........................................ 5  │
│  3. Tools and Technologies Used ................................ 8  │
│  4. Project Database Design (EER/UML) ......................... 12  │
│  5. GUI Discussion - Main Interfaces and Features ............. 18  │
│  6. Recent Enhancements and New Features ...................... 25  │
│  7. Conclusion ................................................. 32  │
│  8. References ................................................. 35  │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### Word Implementation Steps:
1. **Create Title**: 
   - Font: Calibri or Arial, 18pt, Bold
   - Color: Dark Blue (#1f4e79)
   - Alignment: Center
   - Add border: Single line, 1.5pt

2. **Section Entries**:
   - Font: Calibri, 12pt
   - Main sections: Bold
   - Sub-sections: Regular weight, indented 0.5"
   - Use Tab Leaders (dotted lines) to page numbers

3. **Formatting**:
   - Line spacing: 1.15
   - Space after each entry: 6pt
   - Page numbers: Right-aligned, Bold

---

## Design Option 2: Academic Formal Style

### Visual Layout:
```
                        TABLE OF CONTENTS

Abstract ............................................................. ii

1. INTRODUCTION ..................................................... 1
   1.1 Project Overview ............................................. 1
   1.2 Objectives ................................................... 2

2. PROJECT REQUIREMENTS ............................................. 3
   2.1 Functional Requirements ...................................... 3
   2.2 Non-Functional Requirements .................................. 4

3. TOOLS AND TECHNOLOGIES USED ..................................... 5
   3.1 Frontend Technologies ........................................ 5
   3.2 Backend Technologies ......................................... 6
   3.3 Database Technologies ........................................ 7

4. PROJECT DATABASE DESIGN (EER/UML) .............................. 8
   4.1 Database Schema Overview ..................................... 8
   4.2 Entity Relationship Diagram ................................. 9

5. GUI DISCUSSION - MAIN INTERFACES AND FEATURES .................. 10
   5.1 User Interfaces ............................................. 10
   5.2 Administrative Interfaces ................................... 15

6. RECENT ENHANCEMENTS AND NEW FEATURES ........................... 20
   6.1 Recently Viewed Products System ............................. 20
   6.2 Product Color Variant System ................................ 22

7. CONCLUSION ...................................................... 25

8. REFERENCES ...................................................... 27
```

### Word Implementation Steps:
1. **Title Formatting**:
   - Font: Times New Roman, 14pt, Bold, All Caps
   - Alignment: Center
   - Space after: 18pt

2. **Main Sections**:
   - Font: Times New Roman, 12pt, Bold, All Caps
   - Numbering: 1., 2., 3., etc.

3. **Sub-sections**:
   - Font: Times New Roman, 11pt, Regular
   - Numbering: 1.1, 1.2, etc.
   - Indent: 0.25"

---

## Design Option 3: Modern Minimalist Style

### Visual Layout:
```
Contents

01  Introduction                                                    3
02  Project Requirements                                            5
03  Tools and Technologies Used                                     8
04  Project Database Design                                        12
05  GUI Discussion                                                 18
06  Recent Enhancements                                            25
07  Conclusion                                                     32
08  References                                                     35
```

### Word Implementation Steps:
1. **Title**: 
   - Font: Segoe UI, 24pt, Light
   - Color: Dark Gray (#404040)
   - No underline or border

2. **Entries**:
   - Font: Segoe UI, 11pt
   - Numbers: Bold, colored (#0078d4)
   - Titles: Regular weight
   - Page numbers: Right-aligned, Light weight

---

## Design Option 4: Corporate Style with Icons

### Visual Layout:
```
┌─────────────────────────────────────────────────────────────┐
│                    📋 TABLE OF CONTENTS                     │
└─────────────────────────────────────────────────────────────┘

🏠 1. Introduction .................................................. 3

📋 2. Project Requirements .......................................... 5
    ✓ Functional Requirements
    ⚙️ Non-Functional Requirements

🛠️ 3. Tools and Technologies Used .................................. 8
    💻 Frontend Technologies
    🔧 Backend Technologies
    🗄️ Database Technologies

🗂️ 4. Project Database Design ..................................... 12

🖥️ 5. GUI Discussion .............................................. 18
    👤 User Interfaces
    👨‍💼 Administrative Interfaces

🆕 6. Recent Enhancements .......................................... 25

📝 7. Conclusion ................................................... 32

📚 8. References ................................................... 35
```

### Word Implementation Steps:
1. **Add Icons**: Insert → Symbols → More Symbols → Webdings/Wingdings
2. **Color Scheme**: Use consistent colors for different types of content
3. **Spacing**: Extra space between major sections

---

## Quick Word Setup Instructions

### Method 1: Automatic Table of Contents
1. **Apply Heading Styles** to your document sections:
   - Heading 1 for main sections
   - Heading 2 for sub-sections

2. **Insert TOC**:
   - References tab → Table of Contents
   - Choose "Custom Table of Contents"
   - Select format and modify as needed

3. **Customize Appearance**:
   - Right-click TOC → Modify
   - Change fonts, colors, and spacing

### Method 2: Manual Creation
1. **Create Table**:
   - Insert → Table → 2 columns, 8+ rows
   - Remove borders or keep minimal borders

2. **Format Columns**:
   - Left column: Section titles
   - Right column: Page numbers
   - Use tab leaders for dotted lines

### Professional Formatting Tips:

1. **Typography**:
   - Use consistent font family throughout
   - Hierarchy: Title (largest) → Main sections → Sub-sections
   - Recommended fonts: Calibri, Arial, Times New Roman, Segoe UI

2. **Colors**:
   - Professional: Navy blue (#1f4e79), Dark gray (#404040)
   - Modern: Blue (#0078d4), Teal (#008080)
   - Academic: Black or very dark gray

3. **Spacing**:
   - Consistent spacing between entries
   - Extra space before major sections
   - Proper indentation for hierarchy

4. **Page Numbers**:
   - Right-aligned
   - Use tab leaders (dots) to connect titles to numbers
   - Consider using Roman numerals (i, ii, iii) for preliminary pages

### Advanced Features:
- **Hyperlinks**: Make TOC entries clickable (Insert → Hyperlink)
- **Bookmarks**: Create bookmarks for each section for easy navigation
- **Cross-references**: Use Word's cross-reference feature for automatic page number updates

Choose the design that best fits your document's purpose:
- **Academic papers**: Option 2 (Formal)
- **Business reports**: Option 1 (Professional) or Option 4 (Corporate)
- **Modern presentations**: Option 3 (Minimalist)
