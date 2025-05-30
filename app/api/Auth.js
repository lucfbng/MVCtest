import ToastService from "../services/Toast.service.js";

document.getElementById("registerForm").addEventListener("submit", async function (e) {
  e.preventDefault();
  const formData = new URLSearchParams(new FormData(this));
  // Préciser l'action pour que le controlleur sache quel fonction appeller
  formData.append('action', 'register');
  try {
    const res = await fetch("http://localhost/MVCtest/app/controllers/Auth.controller.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: formData,
    });
    const data = await res.json();
    /* ToastService.show(data.message, data.success ? 'success' : 'error'); */
    if (data.success) {
      ToastService.show(data.message, 'success');
    } else {
      // Si plusieurs message sont envoyés un toast est affiché par message
      if (data.message && Array.isArray(data.message) && data.message.length > 0) {
        data.message.forEach(msg => {
          ToastService.show(msg, 'error');
        });
      } else {
        ToastService.show(data.message, 'error');
      }
    }
  } catch (errors) {
    ToastService.show('Erreur de connexion au serveur', 'error');
    console.log(errors);
  }
});
