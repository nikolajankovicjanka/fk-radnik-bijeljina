import { computed, ref, watch } from 'vue'
import { defineStore } from 'pinia'

export interface CartItemCustomization {
  name?: string
  number?: string
}

export interface CartItem {
  key: string

  productId: number
  variantId: number

  slug: string
  name: string
  sku: string

  variantSize: string
  variantSku: string

  image: string | null

  unitPrice: number
  originalPrice: number | null

  quantity: number
  availableQuantity: number

  isCustomizable: boolean
  customization: CartItemCustomization | null
}

export interface AddToCartPayload {
  productId: number
  variantId: number

  slug: string
  name: string
  sku: string

  variantSize: string
  variantSku: string

  image: string | null

  unitPrice: number
  originalPrice?: number | null

  quantity: number
  availableQuantity: number

  isCustomizable: boolean
  customization?: CartItemCustomization | null
}

const STORAGE_KEY = 'fk-radnik-shop-cart'

function createItemKey(payload: {
  productId: number
  variantId: number
  customization?: CartItemCustomization | null
}) {
  const customizationName = payload.customization?.name?.trim().toLocaleUpperCase('bs-BA') ?? ''

  const customizationNumber = payload.customization?.number?.trim() ?? ''

  return [payload.productId, payload.variantId, customizationName, customizationNumber].join(':')
}

function loadStoredCart(): CartItem[] {
  if (typeof window === 'undefined') {
    return []
  }

  try {
    const stored = window.localStorage.getItem(STORAGE_KEY)

    if (!stored) {
      return []
    }

    const parsed = JSON.parse(stored)

    if (!Array.isArray(parsed)) {
      return []
    }

    return parsed
  } catch (error) {
    console.error('Unable to load shop cart:', error)

    return []
  }
}

export const useCartStore = defineStore('shop-cart', () => {
  const items = ref<CartItem[]>(loadStoredCart())

  const totalItems = computed(() => items.value.reduce((total, item) => total + item.quantity, 0))

  const subtotal = computed(() =>
    items.value.reduce((total, item) => total + item.unitPrice * item.quantity, 0)
  )

  const isEmpty = computed(() => items.value.length === 0)

  function addItem(payload: AddToCartPayload) {
    const key = createItemKey(payload)

    const existingItem = items.value.find(item => item.key === key)

    const requestedQuantity = Math.max(1, payload.quantity)

    if (existingItem) {
      existingItem.availableQuantity = payload.availableQuantity

      existingItem.unitPrice = payload.unitPrice

      existingItem.quantity = Math.min(
        existingItem.quantity + requestedQuantity,
        payload.availableQuantity
      )

      return
    }

    items.value.push({
      key,

      productId: payload.productId,
      variantId: payload.variantId,

      slug: payload.slug,
      name: payload.name,
      sku: payload.sku,

      variantSize: payload.variantSize,
      variantSku: payload.variantSku,

      image: payload.image,

      unitPrice: payload.unitPrice,
      originalPrice: payload.originalPrice ?? null,

      quantity: Math.min(requestedQuantity, payload.availableQuantity),

      availableQuantity: payload.availableQuantity,

      isCustomizable: payload.isCustomizable,

      customization: payload.customization ?? null,
    })
  }

  function removeItem(key: string) {
    items.value = items.value.filter(item => item.key !== key)
  }

  function setQuantity(key: string, quantity: number) {
    const item = items.value.find(cartItem => cartItem.key === key)

    if (!item) {
      return
    }

    if (quantity <= 0) {
      removeItem(key)
      return
    }

    item.quantity = Math.min(quantity, item.availableQuantity)
  }

  function increaseQuantity(key: string) {
    const item = items.value.find(cartItem => cartItem.key === key)

    if (!item) {
      return
    }

    setQuantity(key, item.quantity + 1)
  }

  function decreaseQuantity(key: string) {
    const item = items.value.find(cartItem => cartItem.key === key)

    if (!item) {
      return
    }

    setQuantity(key, item.quantity - 1)
  }

  function clearCart() {
    items.value = []
  }

  function formatMoney(value: number) {
    return `${value.toFixed(2).replace('.', ',')} KM`
  }

  watch(
    items,
    value => {
      if (typeof window === 'undefined') {
        return
      }

      window.localStorage.setItem(STORAGE_KEY, JSON.stringify(value))
    },
    {
      deep: true,
    }
  )

  return {
    items,

    totalItems,
    subtotal,
    isEmpty,

    addItem,
    removeItem,
    setQuantity,
    increaseQuantity,
    decreaseQuantity,
    clearCart,

    formatMoney,
  }
})
