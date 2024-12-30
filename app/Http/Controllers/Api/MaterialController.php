<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use App\Services\MaterialService;
use App\Http\Requests\StoreMaterialRequest;
use App\Http\Requests\UpdateMaterialRequest;

class MaterialController extends Controller
{

    protected $materialService
    ;

    /*
     * Constructor to inject the materialService
     * @param materialService
     *  $materialService
     * 
     */
    public function __construct(MaterialService $materialService
    )
    {
        $this->materialService= $materialService;
    }

    /*
     * Display a listing of materials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()

    {   
        $materials = $this->materialService
        ->getAllMaterial();
        if (!$materials) {
            return $this->error('Getting materials failed');
        }
        if (empty($materials)) {
            return $this->success(null, 'there is no material in database', 200);
        }
        else 
            return $this->success($materials,'all materials retrieved successfully.',200);
    }

    /*
     * Store a newly created material in storage.
     *
     * @param StorematerialRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreMaterialRequest $request)
    {  
        $validatedData = $request->validated();
        $material = $this->materialService->creatematerial($validatedData);
        if (!$material) {
            return $this->error('Creating material faild');
        } 
        return $this->success($material,'material created successfully.',201);  

    }

    /*
     * Display the specified material.
     *
     * @param material $material
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Material $material)
    {   
            $material = $this->materialService
            ->getmaterial($material);
            if (!$material) {
                return $this->error('Return material faild');
            } 
            return $this->success($material, 'Return material successfully.', 200);
    }

    /*
     * Update the specified material in storage.
     *
     * @param UpdatematerialRequest $request
     * @param material $material
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateMaterialRequest $request, material $material)
    {  
        $validatedData = $request->validated();
        $material = $this->materialService->updatematerial($material, $validatedData);
        if (!$material) {
            return $this->error('Updating material faild');
        } 
        return $this->success($material, 'material updated successfully.', 200);

    }

    /*
     * Remove the specified material from storage.
     *
     * @param material $material
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Material $material)
    {   
        $this->materialService->deleteMaterial($material);
        return $this->success('material deleted successfully.',200);

    }
}

