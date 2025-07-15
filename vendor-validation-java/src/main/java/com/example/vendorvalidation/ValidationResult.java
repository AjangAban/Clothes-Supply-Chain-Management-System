package com.example.vendorvalidation;

public class ValidationResult {
    private double financialStability;
    private double reputation;
    private double compliance;
    private String notes;
    private String status; // "approved" or "rejected"
    private String vendorName;
    private String pdfFileName;
    private boolean visitScheduled;

    public double getFinancialStability() { return financialStability; }
    public void setFinancialStability(double financialStability) { this.financialStability = financialStability; }

    public double getReputation() { return reputation; }
    public void setReputation(double reputation) { this.reputation = reputation; }

    public double getCompliance() { return compliance; }
    public void setCompliance(double compliance) { this.compliance = compliance; }

    public String getNotes() { return notes; }
    public void setNotes(String notes) { this.notes = notes; }

    public String getStatus() { return status; }
    public void setStatus(String status) { this.status = status; }

    public String getVendorName() { return vendorName; }
    public void setVendorName(String vendorName) { this.vendorName = vendorName; }

    public String getPdfFileName() { return pdfFileName; }
    public void setPdfFileName(String pdfFileName) { this.pdfFileName = pdfFileName; }

    public boolean isVisitScheduled() { return visitScheduled; }
    public void setVisitScheduled(boolean visitScheduled) { this.visitScheduled = visitScheduled; }
} 