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
 * @param array $filters
 * @param int $perPage
 * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
 */
public function getCategories(?string $name = null, array $filters = [], int $perPage = 15)
{
    try {
        $cacheKey = 'categories_' . md5(json_encode($filters) . $perPage . request('page', 1));

        
        return cache()->remember($cacheKey, 60, function () use ($name, $filters, $perPage) {
            $query = Category::with('courses'); 
            if ($name) {
                $query->where('name', 'LIKE', '%' . $name . '%');
            }

           
            foreach ($filters as $key => $value) {
                $query->where($key, $value);
            }

            return $query->paginate($perPage); 
        });
    } catch (\Exception $e) {
        throw new \Exception('Failed to fetch categories ');
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
            throw new \Exception('Failed to fetch category details ');
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
            throw new \Exception('Failed to create category ' );
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
            throw new \Exception('Failed to update category ' );
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
            throw new \Exception('Failed to delete category ' );
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
            throw new \Exception('Failed to fetch trashed categories ');
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
            throw new \Exception('Failed to restore category ' );
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
            throw new \Exception('Failed to force delete category ');
        }
}
}