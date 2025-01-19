<?php
namespace App\Services;

use Exception;
use App\Models\Material;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Exceptions\HttpResponseException;

class MaterialService{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }
     /*
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

     /* create new material
     *
     * @param array $data of the material to create
     * @return array An array containing data (created material)
     * @throws HttpResponseException If an error occurs during database interaction.
     */
    public function createMaterial(array $data)
{  try {
    $fileData = $this->fileService->storeFile($data['file_path']);
    $videoData = isset($data['video_path']) && $data['video_path'] instanceof UploadedFile
        ? $this->fileService->storeVideo($data['video_path'])
        : [];

    $material = Material::create([
        'title' => $data['title'],
        'file_path' => $fileData['file_path'],
        'video_path' => $videoData['video_path'] ?? null,
        'course_id' => $data['course_id'],
    ]);

    return $material;
} catch (Exception $e) {
    Log::error('Error creating material: ' . $e->getMessage());
    throw new HttpResponseException(response()->json(['message' => 'Failed to create material.'], 500));
}

}

        /*
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

   //..............................
 
    /*
     * update material's data if exists
     * @param \App\Models\Material $material
     * @param array $data
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return Material
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
/*
 * Delete Material 
 * 
 * @param Material $material,the material model instance 
 *  @throws HttpResponseException If an error occurs during database interaction.
 */

 public function deleteMaterial(Material $material)
 {
     try {
         $material->delete();
 
         return true;
     } catch (Exception $e) {
         Log::error('Error deleting material: ' . $e->getMessage());
 
         return false;
     }
 }

 //................................................
/*
 * Restore Material
 * @param mixed $id
 * @throws \Exception
 * @throws \Illuminate\Http\Exceptions\HttpResponseException
 * @return array|mixed|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|null
 */
public function restoreMaterial($id)
{
    try {
         $material = Material::onlyTrashed()->find($id);

         if(is_null($material))
         {
            throw new Exception("This id is not Deleted yet,or dosn't exist!!");
         }
         $material->restore();
         return $material;

    } catch (Exception $e) {
        Log::error('Error while  Restoring Material ' . $e->getMessage());
        throw new HttpResponseException(response()->json(['message' => 'Failed in the server '], 500));
    }

}
 /*
  * get All the Trashed Material
  * @throws \Exception
  * @throws \Illuminate\Http\Exceptions\HttpResponseException
  * @return array|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
  */
  public function getAllTrashedMaterial()
  {
     try {
        $materials = Material::onlyTrashed()->get();
         if($materials->isEmpty())
         {
            return collect([]);
         }
         return $materials;
     } catch (Exception $e) {
         Log::error('Error while  get all trashed materials ' . $e->getMessage());
         throw new HttpResponseException(response()->json(['message' => 'Failed in the server  '], 500));
     }
  }
 

}



?>


