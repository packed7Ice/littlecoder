import { useState } from 'react';
import { useUser } from '../lib/useUser';

interface NameInputModalProps {
  isOpen: boolean;
  onClose: () => void;
}

export function NameInputModal({ isOpen, onClose }: NameInputModalProps) {
  const { userName, setUserName } = useUser();
  const [inputValue, setInputValue] = useState(userName);

  if (!isOpen) return null;

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (inputValue.trim()) {
      setUserName(inputValue.trim());
      onClose();
    }
  };

  return (
    <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
      <div className="card p-6 w-full max-w-md mx-4">
        <h2 className="text-xl font-bold mb-4 text-white">名前を入力</h2>
        <form onSubmit={handleSubmit}>
          <input
            type="text"
            value={inputValue}
            onChange={(e) => setInputValue(e.target.value)}
            placeholder="あなたの名前"
            className="w-full p-3 rounded-lg bg-slate-700 border border-slate-600 text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500"
            autoFocus
            maxLength={20}
          />
          <div className="flex gap-3 mt-4">
            <button
              type="button"
              onClick={onClose}
              className="flex-1 btn bg-slate-700 hover:bg-slate-600 text-white"
            >
              キャンセル
            </button>
            <button
              type="submit"
              disabled={!inputValue.trim()}
              className="flex-1 btn btn-primary"
            >
              保存
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

export default NameInputModal;
