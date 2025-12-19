import { useState, type ReactNode } from 'react';
import { UserContext } from './userContextDef';

export function UserProvider({ children }: { children: ReactNode }) {
  // ローカルストレージから初期値を読み込み（useState の初期化関数として）
  const [userName, setUserNameState] = useState<string>(() => {
    if (typeof window !== 'undefined') {
      return localStorage.getItem('littlecoder_username') || '';
    }
    return '';
  });

  const setUserName = (name: string) => {
    setUserNameState(name);
    localStorage.setItem('littlecoder_username', name);
  };

  return (
    <UserContext.Provider value={{ userName, setUserName }}>
      {children}
    </UserContext.Provider>
  );
}
