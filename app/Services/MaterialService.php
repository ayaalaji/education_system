<?php
namespace App\Services;

use Exception;
use App\Models\Material;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use App\Events\CourseSessionUploadedEvent;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Cache;

class MaterialService{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }
/**
     * return a list of materials by spacific cource.
     *
     * @return array An array containing data .
     * @throws HttpResponseException If an error occurs during database interaction.
     */
    public function getAllMaterial(){
        try{
        $cacheKey = 'materials_' . md5(request('page', 1));
        return cacheData($cacheKey,function(){
            $materials=Material::select('title')->get();
            return $materials;
        });


        }catch (Exception $e) {
            Log::error('Error getting materials: ' . $e->getMessage());
            throw new HttpResponseException(response()->json(['message' => 'Failed to retrieve materials.'], 500));
        }

        }
    /**
     * create new material
     * @param array $data
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return Material|\Illuminate\Database\Eloquent\Model
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

    cache()->forget('materials_' . md5(request('page', 1)));

    //Lanch an Event after creat new material
    event(new CourseSessionUploadedEvent($material));

    return $material;
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
            return cacheData("material_{$material->id}",function() use($material){
                return $material;

            });
        }catch(Exception $e){
        Log::error('Error getting Material'.$e->getMessage());
        throw new HttpResponseException(response()->json(['message' => 'Failed to retrieve material.'], 500));
        }
    }

   //..............................

    /**
     * update material's data if exists
     * @param \App\Models\Material $material
     * @param array $data
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return Material
     */
    public function updateMaterial(Material $material,array $data){
        try{
            $material->update(array_filter($data));//remove the feild which null value
            Cache::forget("material_{$material->id}");
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

 public function deleteMaterial(Material $material)
 {
     try {
         $material->delete();
         Cache::forget("material_{$material->id}");

         return true;
     } catch (Exception $e) {
         Log::error('Error deleting material: ' . $e->getMessage());

         return false;
     }
 }
}




?>


