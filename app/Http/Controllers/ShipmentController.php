<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    /**
     * Menampilkan semua metode pengiriman
     */
    public function index()
    {
        return response()->json(Shipment::latest()->get());
    }

    /**
     * Menyimpan metode pengiriman baru
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:shipments,name',
            'description' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
        ]);

        $shipment = Shipment::create($data);

        return response()->json($shipment, 201);
    }

    /**
     * Menampilkan detail (tidak terpakai di view, tapi bagus untuk ada)
     */
    public function show(Shipment $shipment)
    {
        return response()->json($shipment);
    }

    /**
     * Meng-update metode pengiriman
     */
    public function update(Request $request, Shipment $shipment)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:shipments,name,' . $shipment->id,
            'description' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
        ]);

        $shipment->update($data);

        return response()->json($shipment);
    }


    public function destroy(Shipment $shipment)
    {
        $shipment->delete();
        return response()->json(null, 204);
    }

}