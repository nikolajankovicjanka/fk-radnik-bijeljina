import type {
  CreateShopOrderRequest,
  CreateShopOrderResponse,
  ShopApiErrorResponse,
  ShopPricingRequest,
  ShopPricingResponse,
  ShopProductResponse,
  ShopProductsResponse,
} from '@/types/shop'

const SHOP_API_URL = '/api/shop'

export class ShopApiError extends Error {
  status: number
  errors: Record<string, string[]>

  constructor(message: string, status: number, errors: Record<string, string[]> = {}) {
    super(message)

    this.name = 'ShopApiError'
    this.status = status
    this.errors = errors
  }
}

async function request<T>(url: string, options: RequestInit = {}): Promise<T> {
  const response = await fetch(url, {
    ...options,

    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      ...options.headers,
    },
  })

  let body: T | ShopApiErrorResponse | null = null

  try {
    body = await response.json()
  } catch {
    body = null
  }

  if (!response.ok) {
    const errorBody = body as ShopApiErrorResponse | null

    throw new ShopApiError(
      errorBody?.message || 'Došlo je do greške. Pokušajte ponovo.',
      response.status,
      errorBody?.errors || {}
    )
  }

  return body as T
}

export const shopService = {
  async getProducts(): Promise<ShopProductsResponse> {
    return request<ShopProductsResponse>(`${SHOP_API_URL}/products`)
  },

  async getProduct(slug: string): Promise<ShopProductResponse> {
    return request<ShopProductResponse>(`${SHOP_API_URL}/products/${encodeURIComponent(slug)}`)
  },

  async calculatePricing(payload: ShopPricingRequest): Promise<ShopPricingResponse> {
    return request<ShopPricingResponse>(`${SHOP_API_URL}/pricing`, {
      method: 'POST',
      body: JSON.stringify(payload),
    })
  },

  async createOrder(payload: CreateShopOrderRequest): Promise<CreateShopOrderResponse> {
    return request<CreateShopOrderResponse>(`${SHOP_API_URL}/orders`, {
      method: 'POST',
      body: JSON.stringify(payload),
    })
  },
}
