import { BrowserRouter, Routes, Route } from 'react-router-dom';
import { UserProvider } from './lib/UserContext';
import { Home } from './pages/Home';
import { Problem } from './pages/Problem';

function App() {
  return (
    <UserProvider>
      <BrowserRouter>
        <Routes>
          <Route path="/" element={<Home />} />
          <Route path="/problem/:id" element={<Problem />} />
        </Routes>
      </BrowserRouter>
    </UserProvider>
  );
}

export default App;
