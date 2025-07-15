package com.example.vendorvalidation;

import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.multipart.MultipartFile;
import org.springframework.beans.factory.annotation.Autowired;
import java.io.File;
import java.util.ArrayList;
import java.util.List;
import org.springframework.mail.javamail.JavaMailSender;
import org.springframework.mail.javamail.MimeMessageHelper;
import org.springframework.beans.factory.annotation.Value;
import jakarta.mail.internet.MimeMessage;

@RestController
public class VendorValidationController {
    @Autowired
    private VendorValidationService validationService;

    // In-memory lists for demo; replace with DB in production
    private static final List<ValidationResult> allApplications = new ArrayList<>();
    private static final List<ValidationResult> approvedApplications = new ArrayList<>();

    @Autowired(required = false)
    private JavaMailSender mailSender;

    @Value("${admin.email:admin@example.com}")
    private String adminEmail;

    @PostMapping("/validate-vendor")
    public ResponseEntity<ValidationResult> validateVendor(@RequestParam("pdf") MultipartFile pdf) {
        try {
            ValidationResult result = validationService.validate(pdf);
            allApplications.add(result); // Store every application
            if ("approved".equals(result.getStatus())) {
                // Save PDF for admin dashboard
                String basePath = System.getProperty("user.dir") + File.separator + "admin-pdf-uploads";
                File dir = new File(basePath);
                if (!dir.exists()) dir.mkdirs();
                File dest = new File(dir, pdf.getOriginalFilename());
                pdf.transferTo(dest);
                // Add to in-memory list for admin dashboard
                approvedApplications.add(result);
                // Notify admin (email)
                if (mailSender != null) {
                    try {
                        MimeMessage message = mailSender.createMimeMessage();
                        MimeMessageHelper helper = new MimeMessageHelper(message, true);
                        helper.setTo(adminEmail);
                        helper.setSubject("New Vendor Application Approved: Facility Visit Scheduled");
                        helper.setText("Vendor: " + result.getVendorName() + "\nPDF: " + result.getPdfFileName() + "\nStatus: " + result.getStatus() + "\nNotes: " + result.getNotes());
                        helper.addAttachment(result.getPdfFileName(), dest);
                        mailSender.send(message);
                    } catch (Exception mailEx) {
                        System.err.println("Failed to send admin email: " + mailEx.getMessage());
                    }
                }
            }
            return ResponseEntity.ok(result);
        } catch (Exception e) {
            ValidationResult error = new ValidationResult();
            error.setStatus("rejected");
            error.setNotes("Error processing PDF: " + e.getMessage());
            return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(error);
        }
    }

    @GetMapping("/admin/applications")
    public List<ValidationResult> getAllApplications() {
        return allApplications;
    }

    public static List<ValidationResult> getAllApplicationsStatic() {
        return allApplications;
    }

    @GetMapping("/admin/approved-applications")
    public List<ValidationResult> getApprovedApplications() {
        return approvedApplications;
    }
} 