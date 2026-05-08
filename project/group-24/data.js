// =========================
// REAL BACKEND ONLY (NO LOCALSTORAGE / NO FAKE DATA)
// =========================
const BASE_URL = "http://localhost/project/group-24";

const DB = {

  async getMenu() {
    try {
      const res = await fetch(`${BASE_URL}/get_menu.php`);
      if (!res.ok) throw new Error("Failed to load menu");
      return await res.json();
    } catch (err) {
      console.error("getMenu error:", err);
      return [];
    }
  },

  async getItem(id) {
    try {
      const res = await fetch(`${BASE_URL}/get_menu.php`);
      if (!res.ok) throw new Error("Failed to load item");
      const data = await res.json();
      return data.find(i => i.id == id) || null;
    } catch (err) {
      console.error("getItem error:", err);
      return null;
    }
  },

  async filterByCategory(category) {
    try {
      const res = await fetch(`${BASE_URL}/get_menu_by_category.php?category=${encodeURIComponent(category)}`);
      if (!res.ok) throw new Error("Failed to filter menu");
      return await res.json();
    } catch (err) {
      console.error("filterByCategory error:", err);
      return [];
    }
  },

  async getAnnouncements() {
    try {
      const res = await fetch(`${BASE_URL}/get_announcements.php`);
      if (!res.ok) throw new Error("Failed to load announcements");
      return await res.json();
    } catch (err) {
      console.error("getAnnouncements error:", err);
      return [];
    }
  },

  currentUser() {
    try {
      return JSON.parse(sessionStorage.getItem("andiks_user")) || null;
    } catch {
      return null;
    }
  },

  setUser(user) {
    sessionStorage.setItem("andiks_user", JSON.stringify(user));
  },

  logout() {
    sessionStorage.removeItem("andiks_user");
  },

  stars(rating, size = '') {
    let html = '';
    for (let i = 1; i <= 5; i++)
      html += `<i class="bi bi-star${i <= Math.round(rating) ? '-fill' : ''} ${size}" style="color:#F5C800"></i>`;
    return html;
  },

  getDayName(dayNum = null) {
    const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    return days[dayNum !== null ? dayNum : new Date().getDay()];
  },

  getTodayFormatted() {
    return new Date().toLocaleDateString('en-US', {
      weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
    });
  }

};