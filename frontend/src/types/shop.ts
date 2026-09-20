export interface ShopCategory {
  id: number
  name: string
  slug: string
  description: string | null
  sort_order: number
  is_active: boolean
}

export interface ShopProductVariant {
  id: number
  shop_product_id: number
  size: string
  sku: string
  stock_quantity: number
  reserved_quantity: number
  is_active: boolean
  sort_order: number
}

export interface ShopProductImage {
  id: number
  shop_product_id: number
  image_path: string
  sort_order: number
}

export interface ShopProduct {
  id: number
  shop_category_id: number
  name: string
  slug: string
  sku: string
  short_description: string | null
  description: string | null
  price: string
  sale_price: string | null
  main_image: string | null
  is_active: boolean
  is_new: boolean
  is_bestseller: boolean
  is_customizable: boolean
  sort_order: number
  category: ShopCategory
  variants: ShopProductVariant[]
  images?: ShopProductImage[]
}

export interface ShopProductsResponse {
  data: ShopProduct[]
}

export interface ShopProductResponse {
  data: ShopProduct
}

/*
|--------------------------------------------------------------------------
| Pricing
|--------------------------------------------------------------------------
*/

export type ShopDeliveryMethod = 'courier' | 'pickup'

export interface ShopPricingItem {
  variant_id: number
  quantity: number
}

export interface ShopPricingRequest {
  items: ShopPricingItem[]
  season_ticket_number: string | null
  voucher_code: string | null
  delivery_method: ShopDeliveryMethod
}

export interface ShopPricingSeasonTicket {
  valid: true
  ticket_number: string
}

export interface ShopPricingVoucher {
  valid: true
  code: string
  name: string
}

export interface ShopPricingData {
  subtotal: number
  discount_type: string | null
  discount_percent: number | null
  discount_amount: number
  shipping_amount: number
  total: number
  season_ticket: ShopPricingSeasonTicket | null
  voucher: ShopPricingVoucher | null
}

export interface ShopPricingResponse {
  success: true
  data: ShopPricingData
}

/*
|--------------------------------------------------------------------------
| API errors
|--------------------------------------------------------------------------
*/

export interface ShopApiErrorResponse {
  success?: false
  message?: string
  errors?: Record<string, string[]>
}

/*
|--------------------------------------------------------------------------
| Checkout
|--------------------------------------------------------------------------
*/

export interface ShopOrderCustomization {
  name?: string
  number?: string
}

export interface ShopOrderItemRequest {
  variant_id: number
  quantity: number
  customization?: ShopOrderCustomization | null
}

export interface CreateShopOrderRequest {
  first_name: string
  last_name: string
  email: string
  phone: string

  address: string | null
  city: string | null
  postal_code: string | null

  delivery_method: ShopDeliveryMethod

  notes: string | null

  season_ticket_number: string | null
  voucher_code: string | null

  items: ShopOrderItemRequest[]
}

export interface ShopOrderData {
  order_number: string
  status: string

  subtotal: number

  discount_type: string | null
  discount_percent: number | null
  discount_amount: number

  shipping_amount: number
  total: number

  payment_method: string
  delivery_method: ShopDeliveryMethod
}

export interface CreateShopOrderResponse {
  success: true
  message: string
  data: ShopOrderData
}
