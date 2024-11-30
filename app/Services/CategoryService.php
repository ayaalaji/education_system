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
        $query = Category::with('courses'); 
        
        if ($name) {
            $query->where('name', 'LIKE', '%' . $name . '%');
        }
        
        return $query->get();
    }

    /**
     * Get a specific category by ID along with its courses.
     * @param Category $category
    
     * @return \App\Models\Category|null
     */
    public function getCategory(Category $category)
    {
        return $category->with('courses');
    }

    /**
     * Create a new category.
     *
     * @param array $data
     * @return Category
     */
    public function createCategory(array $data): Category
    {
        return Category::create($data);
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
        $category->update($data);
        return $category;
    }

    /**
     * Delete a category.
     *
     * @param Category $category
     * @return void
     */
    public function deleteCategory(Category $category): void
    {
        $category->delete();
    }
}
