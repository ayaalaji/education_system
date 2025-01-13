<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class CategoryService
{
    /**
     * Get all categories with optional filtering by name.
     *
     * @param string|null $name
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCategories(?string $name = null,array $filters, int $perPage)
    {
        try {
            $cacheKey = 'categories_' . md5(json_encode($filters) . $perPage . request('page', 1));
            return cacheData($cacheKey,function () use ($name){

                $query = Category::with('courses');
                if ($name) {
                    $query->where('name', 'LIKE', '%' . $name . '%');
                }
            });



        } catch (\Exception $e) {
            throw new \Exception('Failed to fetch categories: ' . $e->getMessage());
        }
    }

    /**
     * Get a specific category by ID along with its courses.
     *
     * @param Category $category
     * @return \App\Models\Category
     */
    public function getCategory(Category $category)
    {
        try {

            return cacheData("category_{$category->id}", function () use ($category) {
                return $category->load('courses');
            });
        } catch (\Exception $e) {
            throw new \Exception('Failed to fetch category details: ' . $e->getMessage());
        }
    }

    /**
     * Create a new category.
     *
     * @param array $data
     * @return Category
     */
    public function createCategory(array $data): Category
    {
        try {
            cache()->forget('categories_' . md5(json_encode([]) . request('page', 1)));
            return Category::create($data);
        } catch (\Exception $e) {
            throw new \Exception('Failed to create category: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing category.
     *
     * @param Category $category
     * @param array $data
     * @return Category
     */
    public function updateCategory(Category $category, array $data): Category
    {
        try {
            $category->update($data);
            Cache::forget("category_{$category->id}");

            return $category;
        } catch (\Exception $e) {
            throw new \Exception('Failed to update category: ' . $e->getMessage());
        }
    }

    /**
     * Delete a category.
     *
     * @param Category $category
     * @return void
     */
    public function deleteCategory(Category $category): void
    {
        try {
            Cache::forget("category_{$category->id}");
            $category->delete();

        } catch (\Exception $e) {
            throw new \Exception('Failed to delete category: ' . $e->getMessage());
        }
    }
      /**
     * Get trashed categories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTrashedCategories()
    { 
        try {
           $Category=Category::onlyTrashed()->get(); 
           return $Category;
                } catch (\Exception $e) {
            throw new \Exception('Failed to fetch trashed categories: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft-deleted category.
     *
     * @param int $id
     * @return Category
     */
    public function restoreCategory(int $id): Category
    {
        try {
            $category = Category::onlyTrashed()->findOrFail($id); 
            $category->restore(); 
            return $category;
        } catch (\Exception $e) {
            throw new \Exception('Failed to restore category: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete a category.
     *
     * @param int $id
     * @return void
     */
    public function forceDeleteCategory(int $id): void
    {
        try {
            $category = Category::onlyTrashed()->findOrFail($id);
            $category->forceDelete(); 
        } catch (\Exception $e) {
            throw new \Exception('Failed to force delete category: ' . $e->getMessage());
        }
}
}