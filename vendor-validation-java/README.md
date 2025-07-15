# Vendor Validation Java Server

## Overview
This Spring Boot server validates vendor applications submitted as PDFs. It checks for financial stability, reputation, and regulatory adherence. If an application passes, a facility visit is scheduled, the PDF is saved for admin review, and the admin is notified.

## Endpoints

### 1. Submit Vendor Application (PDF)
- **POST** `/validate-vendor`
- **Form field:** `pdf` (file)
- **Response:**
  - If valid: `{ status: "approved", notes: "All criteria met. Scheduling facility visit.", ... }`
  - If invalid: `{ status: "rejected", notes: "Missing or insufficient: ..." }`

### 2. Admin Dashboard: List Approved Applications
- **GET** `/admin/approved-applications`
- **Response:** List of approved applications with vendor name, PDF file name, and status.

## Workflow
1. Vendor uploads PDF via `/validate-vendor`.
2. Server parses and validates the PDF.
3. If approved:
   - Facility visit is scheduled (status set to `visitScheduled=true`).
   - PDF is saved to `admin-pdf-uploads/`.
   - Application is available to admin via `/admin/approved-applications`.
   - (TODO: Email or notify admin.)
4. If rejected, vendor receives reasons in the response.

## Requirements
- Java 19+
- Maven

## Run
```
mvn spring-boot:run
```

## Notes
- For demo, approved applications are stored in-memory. Use a database for production.
- Extend notification logic as needed (email, dashboard, etc.). 
- When an application is approved, an email notification (with PDF attached) is sent to the admin (default: admin@example.com, configurable via `admin.email` property). 