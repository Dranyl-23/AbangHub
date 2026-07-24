declare module '@expo/vector-icons';

export interface User {
  id: number;
  username: string;
  email: string;
  user_type: string;
  phone?: string;
  full_name?: string;
  profile_image?: string;
  avatar_url?: string;
  is_verified?: boolean;
}

export interface Review {
  id: number;
  property_id: number;
  tenant_id: number;
  rating: number;
  comment?: string;
  created_at: string;
  tenant?: User;
}

export interface PropertyImage {
  id: number;
  property_id: number;
  image_path: string;
  image_name?: string;
  is_primary: boolean;
  display_order: number;
}

export interface Property {
  id: number;
  owner_id: number;
  title: string;
  property_type: string;
  bedrooms: number;
  bathrooms: number;
  floor_area?: number;
  monthly_rent: string | number;
  security_deposit?: string | number;
  address: string;
  city: string;
  province: string;
  barangay?: string;
  description?: string;
  status: string;
  furnishing_status: string;
  parking_spaces: number;
  pet_policy: string;
  primary_image?: PropertyImage;
  primaryImage?: PropertyImage; // To match Laravel relation name if not snake_cased
  images?: PropertyImage[];
  owner?: User;
  is_saved?: boolean;
  latitude?: number;
  longitude?: number;
  average_rating?: number;
  review_count?: number;
  reviews?: Review[];
}

export interface Application {
  id: number;
  property_id: number;
  tenant_id: number;
  message?: string;
  status: string; // pending, approved, rejected
  move_in_date?: string;
  occupants?: number;
  created_at: string;
  updated_at: string;
  property?: Property;
  tenant?: User;
  user?: User;
}

export interface Message {
  id: number;
  sender_id: number;
  receiver_id: number;
  property_id?: number;
  content: string;
  is_read: boolean;
  created_at: string;
  updated_at: string;
  sender?: User;
  receiver?: User;
  property?: Property;
}

