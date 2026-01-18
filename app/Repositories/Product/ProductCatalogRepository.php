<?php

namespace App\Repositories\Product;

use Exception;
use App\Helpers\Helper;
use App\Enums\StatusEnum;
use App\Models\Product\Product;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\File;
use App\Models\Product\AttributeValue;
use App\Models\Product\ProductCatalog;

class ProductCatalogRepository
{
    public function __construct(protected ProductCatalog $model) {}

    public function index($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $name         = $request->input('name', null);
        $status       = $request->input('status', null);

        try {
            $productCatalogs = $this->model->with([
                "categories",
                "categories.category:id,name,slug",
                "catalogType:id,name,slug",
                "createdBy:id,username"
            ])
                ->when($name, fn($query) => $query->where("name", "like", "%$name%"))
                ->when($status, fn($query) => $query->where("status", $status))
                ->orderBy('created_at', 'desc')
                ->paginate($paginateSize);

            return $productCatalogs;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function generateFbXmlFeed($request)
    {
        try {
            $categoryIds = $request->input("category_ids", []);

            $products = Product::with(['variations', 'brand', 'category'])
            ->when($categoryIds, fn($query) => $query->whereIn("category_id", $categoryIds))
            ->where("status", StatusEnum::ACTIVE)
            ->get();

            $xml = new \SimpleXMLElement('<rss/>');
            $xml->addAttribute('xmlns:g', 'http://base.google.com/ns/1.0');
            $xml->addAttribute('version', '2.0');

            $channel = $xml->addChild('channel');
            $channel->addChild('title', htmlspecialchars(config('app.name') . ' Products'));
            $channel->addChild('link', config('app.frontend_url'));
            $channel->addChild('description', 'Product List RSS Feed');

            foreach ($products as $product) {
                if ($product->variations->isEmpty()) {
                    $this->addProductToXml($channel, $product);
                } else {
                    foreach ($product->variations as $variation) {
                        $this->addVariationToXml($channel, $product, $variation);
                    }
                }
            }

            // Save XML file
            $fileName = 'facebook_product_catalog_' . time() . '.xml';
            $filePath = public_path("uploads/feeds/{$fileName}");
            File::put($filePath, $xml->asXML());

            // Save metadata to database
            DB::beginTransaction();

            $productCatalog = new $this->model();
            $productCatalog->name = $request->name;
            $productCatalog->slug = $request->name;
            $productCatalog->status = $request->status;
            $productCatalog->url = "uploads/feeds/{$fileName}";
            $productCatalog->product_catalog_type_id = 1;
            $productCatalog->number_of_products = $products->count();
            $productCatalog->save();

            if (!empty($categoryIds)) {
                $categoryDetails = collect($categoryIds)->map(fn($id) => [
                    "product_catalog_id" => $productCatalog->id,
                    "category_id" => $id,
                    "created_at" => now(),
                ])->toArray();

                $productCatalog->categories()->insert($categoryDetails);
            }

            DB::commit();

            return $productCatalog;
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }


    public function show($id)
    {
        try {
            $productCatalog = $this->model->with([
                "categories",
                "categories.category:id,name,slug",
                "catalogType:id,name,slug",
                "createdBy:id,username",
                "updatedBy:id,username"
            ])->find($id);

            if (!$productCatalog) {
                throw new CustomException("Product catalog not found");
            }

            return $productCatalog;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function updateFbXmlFeed($request, $id)
    {
        try {
            $categoryIds = $request->input("category_ids", []);

            $productCatalog = $this->model->find($id);

            if (!$productCatalog) {
                throw new CustomException("Product catalog not found");
            }

            $products = Product::with(['variations', 'brand', 'category'])
                ->when($categoryIds, fn($query) => $query->whereIn("category_id", $categoryIds))
                ->get();

            $xml = new \SimpleXMLElement('<rss/>');
            $xml->addAttribute('xmlns:g', 'http://base.google.com/ns/1.0');
            $xml->addAttribute('version', '2.0');

            $channel = $xml->addChild('channel');
            $channel->addChild('title', htmlspecialchars(config('app.name') . ' Products'));
            $channel->addChild('link', config('app.frontend_url'));
            $channel->addChild('description', 'Product List RSS Feed');

            foreach ($products as $product) {
                if ($product->variations->isEmpty()) {
                    // Case: Product without variations
                    $this->addProductToXml($channel, $product);
                } else {
                    // Case: Product with variations
                    foreach ($product->variations as $variation) {
                        $this->addVariationToXml($channel, $product, $variation);
                    }
                }
            }

            $filePath = public_path($productCatalog->url);

            // Save XML to file
            File::put($filePath, $xml->asXML());

            DB::beginTransaction();

            $productCatalog->name                    = $request->name;
            $productCatalog->slug                    = $request->name;
            $productCatalog->status                  = $request->status;
            $productCatalog->product_catalog_type_id = 1;
            $productCatalog->number_of_products      = $products->count();
            $productCatalog->save();

            $categoryDetails = [];
            foreach ($request->category_ids ?? [] as $categoryId) {
                $categoryDetails[] = [
                    "product_catalog_id" => $productCatalog->id,
                    "category_id"        => $categoryId,
                    "created_at"         => now(),
                ];
            }

            $productCatalog->categories()->delete();
            $productCatalog->categories()->insert($categoryDetails);

            DB::commit();

            return $productCatalog;
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $productCatalog = $this->model->find($id);
            if (!$productCatalog) {
                throw new CustomException("Product catalog not found Policy not found");
            }

            Helper::deleteFile($productCatalog->url);

            $productCatalog->categories()->delete();
            $productCatalog->delete();

            DB::commit();

            return true;
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function trashList($request)
    {
        $paginateSize = Helper::checkPaginateSize($request);
        $name         = $request->input('name', null);
        $status       = $request->input('status', null);

        try {
            $productCatalogs = $this->model->with([
                "categories",
                "categories.category:id,name,slug",
                "catalogType:id,name,slug",
                "createdBy:id,username"
            ])
                ->onlyTrashed()
                ->when($name, fn($query) => $query->where("title", "like", "%$name%"))
                ->when($status, fn($query) => $query->where("status", $status))
                ->orderBy('created_at', 'desc')
                ->paginate($paginateSize);

            return $productCatalogs;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function restore($id)
    {
        try {
            $productCatalog = $this->model->onlyTrashed()->find($id);
            if (!$productCatalog) {
                throw new CustomException("Product catalog not found");
            }

            $productCatalog->restore();

            return $productCatalog;
        } catch (Exception $exception) {

            throw $exception;
        }
    }

    public function permanentDelete($id)
    {
        try {
            $productCatalog = $this->model->onlyTrashed()->find($id);
            if (!$productCatalog) {
                throw new CustomException("Product catalog not found");
            }

            return $productCatalog->forceDelete();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    protected function addProductToXml($channel, $product)
    {
        $frontendUrl = config("app.frontend_url");
        $productDetailsLink = "$frontendUrl/product-details/$product->slug";

        $item = $channel->addChild('item');
        $item->addChild('g:id', $product->id, 'http://base.google.com/ns/1.0');
        $item->addChild('g:quantity_to_sell_on_facebook', $product->current_stock, 'http://base.google.com/ns/1.0');
        $item->addChild('g:description', htmlspecialchars($product->short_description), 'http://base.google.com/ns/1.0');
        $item->addChild('g:condition', 'new', 'http://base.google.com/ns/1.0');
        $item->addChild('g:mpn', $product->id, 'http://base.google.com/ns/1.0');
        $item->addChild('g:title', htmlspecialchars($product->name), 'http://base.google.com/ns/1.0');
        $item->addChild('g:availability', $product->current_stock > 0 ? 'in stock' : 'out of stock', 'http://base.google.com/ns/1.0');
        $item->addChild('g:price', number_format($product->mrp, 2) . ' BDT', 'http://base.google.com/ns/1.0');
        $item->addChild('g:link', $productDetailsLink, 'http://base.google.com/ns/1.0');
        $item->addChild('g:image_link', asset($product->img_path), 'http://base.google.com/ns/1.0');
        $item->addChild('g:sale_price', number_format($product->sell_price, 2) . ' BDT', 'http://base.google.com/ns/1.0');
        $item->addChild('g:item_group_id', $product->id, 'http://base.google.com/ns/1.0');
        $item->addChild('g:custom_label_0', 'recent-product on-sale', 'http://base.google.com/ns/1.0');
        $item->addChild('g:product_type', htmlspecialchars($product->category->name ?? 'All Products'), 'http://base.google.com/ns/1.0');
        $item->addChild('g:shipping_label', 'shipping-charge-2', 'http://base.google.com/ns/1.0');
    }


    protected function addVariationToXml($channel, $product, $variation)
    {
        $frontendUrl = config("app.frontend_url");
        $productDetailsLink = "$frontendUrl/product-details/$product->slug";

        $item = $channel->addChild('item');
        $item->addChild('g:id', $variation->id, 'http://base.google.com/ns/1.0');
        $item->addChild('g:quantity_to_sell_on_facebook', $variation->current_stock, 'http://base.google.com/ns/1.0');
        $item->addChild('g:description', htmlspecialchars($product->short_description), 'http://base.google.com/ns/1.0');
        $item->addChild('g:condition', 'new', 'http://base.google.com/ns/1.0');
        $item->addChild('g:mpn', $variation->id, 'http://base.google.com/ns/1.0');
        $item->addChild('g:title', htmlspecialchars($product->name . ' - ' . $this->getVariationName($variation)), 'http://base.google.com/ns/1.0');
        $item->addChild('g:availability', $variation->current_stock > 0 ? 'in stock' : 'out of stock', 'http://base.google.com/ns/1.0');
        $item->addChild('g:price', number_format($variation->mrp, 2) . ' BDT', 'http://base.google.com/ns/1.0');
        $item->addChild('g:link', $productDetailsLink, 'http://base.google.com/ns/1.0');
        $item->addChild('g:image_link', asset($variation->img_path ?? $product->img_path), 'http://base.google.com/ns/1.0');
        $item->addChild('g:sale_price', number_format($variation->sell_price, 2) . ' BDT', 'http://base.google.com/ns/1.0');
        $item->addChild('g:item_group_id', $product->id, 'http://base.google.com/ns/1.0');
        $item->addChild('g:custom_label_0', 'recent-product on-sale', 'http://base.google.com/ns/1.0');
        $item->addChild('g:product_type', htmlspecialchars($product->category->name ?? 'All Products'), 'http://base.google.com/ns/1.0');
        $item->addChild('g:shipping_label', 'shipping-charge-2', 'http://base.google.com/ns/1.0');
    }

    protected function getVariationName($variation)
    {
        $names = [];
        foreach (['attribute_value_id_1', 'attribute_value_id_2', 'attribute_value_id_3'] as $attributeField) {
            $attributeValue = AttributeValue::find($variation->$attributeField);
            if ($attributeValue) {
                $names[] = $attributeValue->value;
            }
        }

        return implode(', ', $names);
    }

    public function getFbXmlProductCatalog($slug)
    {
        try {
            $productCatalog = $this->model
                ->where("slug", $slug)
                ->where("status", StatusEnum::ACTIVE)
                ->first();

            if (!$productCatalog) {
                throw new CustomException("Product catalog not found");
            }

            $categoryIds = $productCatalog->categories->pluck("category_id");

            $products = Product::with(['variations', 'brand', 'category'])
                ->when(count($categoryIds) > 0, fn($query) => $query->whereIn("category_id", $categoryIds))
                ->where("status", StatusEnum::ACTIVE)
                ->get();

            $data = [
                'title'       => config("app.name"),
                'link'        => url('/'),
                'description' => 'Product Catalog',
                'products'    => []
            ];

            // Populate products and variations
            foreach ($products as $product) {
                if ($product->variations->isEmpty()) {
                    $data['products'][] = $this->getProductJson($product);
                } else {
                    foreach ($product->variations as $variation) {
                        $data['products'][] = $this->getVariationJson($product, $variation);
                    }
                }
            }

            return $data;
        } catch (Exception $exception) {
            throw $exception;
        }
    }


    protected function getProductJson($product)
    {
        return [
            'id'          => $product->id,
            'title'       => $product->name,
            'description' => $product->short_description,
            'link'        => route('products.show', $product->slug),
            'image_link'  => asset($product->img_path),
            'price'       => number_format($product->sell_price, 2) . ' tk',
            'availability' => $product->current_stock > 0 ? 'in stock' : 'out of stock',
            'brand'       => optional($product->brand)->name,
            'product_type' => optional($product->category)->name,
        ];
    }

    protected function getVariationJson($product, $variation)
    {
        return [
            'id'          => $variation->id,
            'title'       => $product->name . ' - ' . $this->getVariationName($variation),
            'description' => $product->short_description,
            'link'        => route('products.show', $product->slug),
            'image_link'  => asset($variation->img_path ?? $product->img_path),
            'price'       => number_format($variation->sell_price, 2) . ' tk',
            'availability' => $variation->current_stock > 0 ? 'in stock' : 'out of stock',
            'brand'       => optional($product->brand)->name,
            'product_type' => optional($product->category)->name,
        ];
    }
}
