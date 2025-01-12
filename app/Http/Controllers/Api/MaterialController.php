<?php

namespace App\Http\Controllers\Api;

use App\Models\Material;
use Illuminate\Http\Request;
use App\Services\MaterialService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\Material\StoreMaterialRequest;
use App\Http\Requests\Material\UpdateMaterialRequest;

class MaterialController extends Controller
{
    protected $materialService;

    /**
     * Constructor to inject the MaterialService
     * @param MaterialService $materialService
     */
    public function __construct(MaterialService $materialService)
    {
        $this->materialService = $materialService;
    }
     /**
     * Display a listing of materials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {   
        $materials = $this->materialService->getAllMaterial();

        if ($materials === null) {
            return $this->error('Failed to retrieve materials.');
        }

        return $this->success($materials, 'All materials retrieved successfully.', 200);
    }

    /**
     * Store a newly created material in storage.
     *
     * @param StoreMaterialRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreMaterialRequest $request)
{ try {
    $validatedData = $request->validated();
    $material = $this->materialService->createMaterial($validatedData);
    return $this->success($material, 'Material created successfully.', 201);
} catch (\Exception $e) {
    Log::error('Error in storing material: ' . $e->getMessage());
    return $this->error('Failed to create material.');
}
}

    /**
     * Display the specified material.
     *
     * @param Material $material
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Material $material)
    {   
        $materialData = $this->materialService->getMaterial($material);

        if (!$materialData) {
            return $this->error('Failed to retrieve material.');
        }

        return $this->success($materialData, 'Material retrieved successfully.', 200);
    }

    /**
     * Update the specified material in storage.
     *
     * @param UpdateMaterialRequest $request
     * @param Material $material
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateMaterialRequest $request, Material $material)
    {  
        $validatedData = $request->validated();
        $updatedMaterial = $this->materialService->updateMaterial($material, $validatedData);

        if (!$updatedMaterial) {
            return $this->error('Failed to update material.');
        }

        return $this->success($updatedMaterial, 'Material updated successfully.', 200);
    }

    /**
     * Remove the specified material from storage.
     *
     * @param Material $material
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Material $material)
    {   
        $deleted = $this->materialService->deleteMaterial($material);

        if (!$deleted) {
            return $this->error('Failed to delete material.');
        }

        return $this->success(null, 'Material deleted successfully.', 200);
    }
}