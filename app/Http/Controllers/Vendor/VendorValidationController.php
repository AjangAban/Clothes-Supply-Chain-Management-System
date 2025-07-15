<?php

// app/Http/Controllers/VendorValidationController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VendorValidationController extends Controller
{
    public function showForm()
    {
        return view('vendor.validate');
    }

    public function validateVendor(Request $request)
    {
        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:2048',
        ]);

        $pdf = $request->file('pdf');

        $response = Http::attach(
            'pdf', file_get_contents($pdf->getRealPath()), $pdf->getClientOriginalName()
        )->post('http://localhost:8080/api/validate');

        if ($response->successful()) {
            return back()->with('success', 'Vendor passed validation ')
                         ->with('data', $response->json());
        }

        return back()->withErrors(['message' => 'Validation failed ']);
    }
}
