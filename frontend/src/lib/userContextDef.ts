import { createContext } from 'react';

interface UserContextType {
  userName: string;
  setUserName: (name: string) => void;
}

export const UserContext = createContext<UserContextType | null>(null);
