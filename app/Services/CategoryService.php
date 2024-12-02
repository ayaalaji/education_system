<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    /**
     * Get all categories with optional filtering by name.
     *
     * @param string|null $name
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCategories(?string $name = null)
    {
        try {
            $query = Category::with('courses');

            if ($name) {
                $query->where('name', 'LIKE', '%' . $name . '%');
            }

            return $query->get();
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
            return $category->load('courses');
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
            $category->delete();
        } catch (\Exception $e) {
            throw new \Exception('Failed to delete category: ' . $e->getMessage());
        }
    }
}
