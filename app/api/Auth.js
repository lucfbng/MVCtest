document.getElementById("registerForm").addEventListener("submit", async function (e) {
  e.preventDefault();
  const formData = new URLSearchParams(new FormData(this));
  try {
    const res = await fetch("http://localhost/MVCtest/app/controllers/Auth.controller.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: formData,
    });
    const data = await res.json();
    if (data.success) {
      console.log(data);
    }
  } catch (error) {
    console.log(error);
  }
});
