declare module '@expo/vector-icons';

export interface User {
  id: number;
  first_name: string;
  last_name: string;
  email: string;
  phone_number?: string;
  role: string;
  full_name?: string;
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
}
