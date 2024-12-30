<?php
namespace App\Services;
use App\Models\Material;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class MaterialService{
/**
     * return a list of materials by spacific cource.
     *
     * @return array An array containing data .
     * @throws HttpResponseException If an error occurs during database interaction.
     */
    public function getAllMaterial(){
        try{
            $materials=Material::select('title')->get();
            return $materials;
        }catch (Exception $e) {
            Log::error('Error getting materials: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed to retrieve materials.'], 500));
        }  

        }
        /**
         * create new material 
         * 
         * @param array $data of the material to create
        * @return array An array containing data (created material)
        * @throws HttpResponseException If an error occurs during database interaction.
        */
        public function createMaterial(array $data)
        {
            try {
                $material = material::create([
                    'title' => $data['title'],
                    'file_path' => $data['file_path'],
                    'video_path' => $data['video_path'],
                    'cours_id' => $data['cours_id']
                ]);
                return $material ;
            } catch (Exception $e) {
                Log::error('Error creating material: ' . $e->getMessage());
                throw new HttpResponseException(response()->json(['message' => 'Failed to create material.'], 500));
            }
        }  
        /**
         * return spacific material
         * 
         * @param Material $material ,material model instance
         * @return $array,array containing data of spacific material 
         * @throws HttpResponseException If an error occurs. This is unlikely here, but good practice.
     */
    public function getMaterial(Material $material){
        try{
            return $material;
        }catch(Exception $e){
        Log::error('Error getting Material'.$e->getMessage());
        throw new HttpResponseException(response()->json(['message' => 'Failed to retrieve material.'], 500));
        }
    }

    /**
     * update material's data if exists
     * 
     * @param Material $material,the material model instance .
      * @param array $data The data to update the material 
     * @return array An array containing  data of updated material 
     * @throws HttpResponseException If an error occurs during database interaction.
     */
   
    public function updateMaterial(Material $material,array $data){
        try{
            $material->update(array_filter($data));//remove the feild which null value 
            return $material;
        }catch (Exception $e) {
            Log::error('Error updating teacher: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed to update material.'], 500));
        }
}
/**
 * Delete Material 
 * 
 * @param Material $material,the material model instance 
 *  @throws HttpResponseException If an error occurs during database interaction.
 */

public function deleteMaterial(Material $material){
    try{
        $material->delete();

    }catch (Exception $e) {
            Log::error('Error deleting material: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed to delete material.'], 500));
        }
    }


}




?>