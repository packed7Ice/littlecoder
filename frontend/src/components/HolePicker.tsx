import type { Hole } from '../types/domain';

interface HolePickerProps {
  holes: Hole[];
  selectedAnswers: number[];
  onAnswerChange: (holeIndex: number, optionId: number) => void;
  disabled?: boolean;
}

export function HolePicker({ holes, selectedAnswers, onAnswerChange, disabled }: HolePickerProps) {
  return (
    <div className="space-y-4">
      <h3 className="text-lg font-semibold text-slate-200">穴埋め選択</h3>
      <div className="grid gap-4 md:grid-cols-2">
        {holes.map((hole, index) => (
          <div
            key={hole.id}
            className="p-4 rounded-lg bg-slate-800/50 border border-slate-700"
          >
            <label className="block mb-2 text-sm font-medium text-slate-300">
              <span className="text-indigo-400">#{index + 1}</span> {hole.label}
            </label>
            <select
              value={selectedAnswers[index] ?? -1}
              onChange={(e) => onAnswerChange(index, parseInt(e.target.value, 10))}
              disabled={disabled}
              className="select-hole w-full"
            >
              <option value={-1} disabled>
                選択してください...
              </option>
              {hole.options.map((option) => (
                <option key={option.id} value={option.id}>
                  {option.code}
                </option>
              ))}
            </select>
          </div>
        ))}
      </div>
    </div>
  );
}

export default HolePicker;
