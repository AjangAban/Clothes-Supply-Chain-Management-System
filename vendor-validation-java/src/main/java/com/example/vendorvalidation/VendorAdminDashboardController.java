package com.example.vendorvalidation;

import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;
import java.util.List;

@Controller
public class VendorAdminDashboardController {
    @GetMapping("/admin/dashboard")
    public String dashboard(Model model) {
        List<ValidationResult> applications = VendorValidationController.getAllApplicationsStatic();
        model.addAttribute("applications", applications);
        return "admin-dashboard";
    }
} 