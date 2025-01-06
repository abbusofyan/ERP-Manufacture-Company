<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Location;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Category::class, 'category');
    }

    public function index(Request $request)
    {
        $search = $request->search;

        if ($request->order && $request->by) {
            $order = $request->order;
            $by = $request->by;
        } else {
            $order = 'id';
            $by = 'desc';
        }

        if ($request->paginate) {
            $paginate = $request->paginate;
        } else {
            $paginate = 10;
        }

        $filters = $request->only(['search']);
        $filters['order'] = $order;
        $filters['by'] = $by;
        $filters['paginate'] = $paginate;

        $categories = Category::withTrashed()->when($search, function ($query) use ($search) {
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('prefix', 'LIKE', "%{$search}%");
        })->when(($order && $by), function ($query) use ($order, $by) {
            $query->orderBy($order, $by);
        })->paginate($paginate);

        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate
        ];

        $categories->appends($queryString);

        return Inertia::render('Category/Index', [
            'categories' => $categories,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('Category/Form');
    }

    public function store(Request $request): Redirector|RedirectResponse|Application
    {
        $request->validate([
            'name' => 'required',
            'prefix' => 'required',
            'file_url' => 'nullable|file|mimes:png,jpg,jpeg|max:5120'
        ]);

        $data = $request->all();

        if ($request->hasFile('file_url')) {
            $path = $request->file('file_url')->store("category", 'public');
            $data['file_url'] = $path;
        }

        Category::create($data);

        return redirect('/categories')->with('created', 'Category created successfully');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function edit(Category $category): Response
    {
        return Inertia::render('Category/Form', ['category' => $category]);
    }

    public function update(Request $request, Category $category): Redirector|RedirectResponse|Application
    {
        $request->validate([
            'name' => 'required',
            'prefix' => 'required',
            'file_url' => 'nullable|file|mimes:png,jpg,jpeg|max:5120'
        ]);

        $data = $request->all();

        if ($request->hasFile('file_url')) {
            /** Delete old file **/
            if (!empty($category->file_url) && Storage::disk('public')->exists($category->file_url)) {
                Storage::disk('public')->delete($category->file_url);
            }

            /** Public new file **/
            $path = $request->file('file_url')->store("categories", 'public');
            $data['file_url'] = $path;
        }

        $category->update($data);

        return redirect('/categories')->with('created', 'Category created successfully');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Category $category
     * @return RedirectResponse
     */
    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return back()->with('deleted', 'Category inactivated successfully');
    }

    public function select2Query(Request $request)
    {
        return Category::when($request->has('search'), function ($query) use ($request) {
            $query->where('prefix', 'LIKE', "%{$request->search}%")
                ->orWhere('name', 'LIKE', "%{$request->search}%");
        })->when($request->has('id') && $request->id != '', function($query) use ($request) {
            $query->where('id', $request->id);
        })->limit(10)->get();
    }

    public function restore($category_id) {
        $category = Category::withTrashed()->find($category_id);
        $category->restore();
        return back()->with('created', 'Category activated successfully');
    }
}
