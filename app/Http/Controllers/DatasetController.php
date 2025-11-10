<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use Illuminate\Http\Request;
use App\Imports\DatasetImport;
use Maatwebsite\Excel\Facades\Excel;
class DatasetController extends Controller
{
    public function index()
    {
        $datasets = Dataset::all();  // Fetch all users from the database
        return view('pages.dataset.index', compact('datasets'));
    }
    public function create()
    {
        return view('pages.dataset.create');  // This will display the form for creating a new dataset
    }

    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'nama_platform_e_wallet' => 'required|string|max:255',
            'VTP' => 'required|string|max:255',
            'NTP' => 'required|string|max:255',
            'PPE' => 'required|string|max:255',
            'FPE' => 'required|string|max:255',
            'PSD' => 'required|string|max:255',
            'IPE' => 'required|string|max:255',
            'PKP' => 'required|string|max:255',
        ]);

        // Create a new dataset record in the database
        Dataset::create([
            'nama_platform_e_wallet' => $request->nama_platform_e_wallet,
            'VTP' => $request->VTP,
            'NTP' => $request->NTP,
            'PPE' => $request->PPE,
            'FPE' => $request->FPE,
            'PSD' => $request->PSD,
            'IPE' => $request->IPE,
            'PKP' => $request->PKP,
        ]);

        // Redirect to the dataset index with a success message
        return redirect()->route('dataset.index')->with('success', 'Dataset created successfully!');
    }

    public function edit($id)
    {
        // Retrieve the dataset by its ID
        $dataset = Dataset::findOrFail($id);

        // Return the edit view with the dataset
        return view('pages.dataset.edit', compact('dataset'));
    }
    public function update(Request $request, $id)
    {
        // Validate the incoming data
        $request->validate([
            'nama_platform_e_wallet' => 'required|string|max:255',
            'VTP' => 'required|string|max:255',
            'NTP' => 'required|string|max:255',
            'PPE' => 'required|string|max:255',
            'FPE' => 'required|string|max:255',
            'PSD' => 'required|string|max:255',
            'IPE' => 'required|string|max:255',
            'PKP' => 'required|string|max:255',
        ]);

        // Find the dataset by ID and update its values
        $dataset = Dataset::findOrFail($id);
        $dataset->update([
            'nama_platform_e_wallet' => $request->nama_platform_e_wallet,
            'VTP' => $request->VTP,
            'NTP' => $request->NTP,
            'PPE' => $request->PPE,
            'FPE' => $request->FPE,
            'PSD' => $request->PSD,
            'IPE' => $request->IPE,
            'PKP' => $request->PKP,
        ]);

        // Redirect back to the dataset index page with a success message
        return redirect()->route('dataset.index')->with('success', 'Dataset updated successfully!');
    }

    public function destroy($id)
    {
        // Find the dataset by ID
        $dataset = Dataset::findOrFail($id);

        // Delete the dataset
        $dataset->delete();

        // Redirect back to the index page with a success message
        return redirect()->route('dataset.index')->with('success', 'Dataset deleted successfully!');
    }
    public function import(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|mimes:xlsx,xls' // Accept only .xlsx or .xls files
        ]);

        // Step 1: Delete all existing data in the 'datasets' table
        Dataset::truncate();  // This will delete all records from the dataset table

        // Step 2: Import the new data from the uploaded Excel file
        Excel::import(new DatasetImport, $request->file('file'));

        // Step 3: Redirect back with a success message
        return redirect()->route('dataset.index')->with('success', 'Dataset imported successfully!');
    }
}