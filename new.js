
document.addEventListener("DOMContentLoaded", () => {

  // ================= MOBILE MENU =================
  const openMenu = document.getElementById("openMenu");
  const closeMenu = document.getElementById("closeMenu");
  const mobileMenu = document.getElementById("mobileMenu");

  openMenu.addEventListener("click", () => {
    mobileMenu.classList.add("active");
  });

  closeMenu.addEventListener("click", () => {
    mobileMenu.classList.remove("active");
  });

  // ================= MOBILE DROPDOWN (ACCORDION) =================
  const sideItems = document.querySelectorAll(".side-item");

  sideItems.forEach(item => {
    item.addEventListener("click", (e) => {

      // prevent closing when clicking links inside dropdown
      if (e.target.tagName === "A") return;

      // close other dropdowns
      sideItems.forEach(other => {
        if (other !== item) {
          other.classList.remove("active");
        }
      });

      // toggle current dropdown
      item.classList.toggle("active");
    });
  });

});

document.addEventListener("DOMContentLoaded", function () {

// ================= GLOBAL =================
let selectedService = "";
let confirmationResult = null;
let isOtpVerified = false;

const otpInputs = document.querySelectorAll(".otp-input");

// ================= SLIDE CONTROL =================
window.goToSlide = function (num) {
    document.querySelectorAll(".form-slide").forEach(s => s.classList.remove("active"));
    const target = document.getElementById("slide" + num);
    if (target) target.classList.add("active");
};

// ================= TYPE CHANGE =================
const educationType = document.getElementById("educationType");

if (educationType) {
    educationType.addEventListener("change", function () {

        ["regularCoursesSection", "industrialSection", "servicesSection", "onlineCoursesSection"]
            .forEach(id => {
                const el = document.getElementById(id);
                if (el) el.style.display = "none";
            });

        if (this.value === "regular") document.getElementById("regularCoursesSection").style.display = "block";
        if (this.value === "industrial") document.getElementById("industrialSection").style.display = "block";
        if (this.value === "services") document.getElementById("servicesSection").style.display = "block";
        if (this.value === "online") document.getElementById("onlineCoursesSection").style.display = "block";
    });
}

// ================= SERVICE SELECT =================
document.querySelectorAll(".service-btn").forEach(btn => {
    btn.addEventListener("click", function () {
        document.querySelectorAll(".service-btn").forEach(b => b.classList.remove("selected"));
        this.classList.add("selected");
        selectedService = this.dataset.service;
    });
});

// ================= VALIDATE SLIDE 1 =================
window.validateSlide1 = function () {
    let type = educationType.value;

    if (!type) {
        alert("Please select type");
        return;
    }

    if (type === "services" && !selectedService) {
        alert("Please select a service");
        return;
    }

    goToSlide(2);
};

// ================= VALIDATE SLIDE 2 =================
window.validateSlide2 = function () {

    let name = document.getElementById("fullName").value.trim();
    let phone = document.getElementById("mobileNumber").value.trim();
    let email = document.getElementById("emailAddress").value.trim();

    if (!name) return alert("Enter name");

    if (!/^[6-9]\d{9}$/.test(phone)) {
        return alert("Enter valid 10 digit number");
    }

    if (!/^\S+@\S+\.\S+$/.test(email)) {
        return alert("Enter valid email");
    }

    goToSlide(3);
};

// ================= FIREBASE =================
 const firebaseConfig = {
  apiKey: "AIzaSyDhOFNMp-SpfKo28_Dse2mNpSYigxyTwxU",
  authDomain: "contactform-netcoder-website.firebaseapp.com",
  projectId: "contactform-netcoder-website",
  storageBucket: "contactform-netcoder-website.firebasestorage.app",
  messagingSenderId: "121917397761",
  appId: "1:121917397761:web:2d75b1a46fd02f4d211900",
  measurementId: "G-7HQZTWRZJ4"
};

firebase.initializeApp(firebaseConfig);
const auth = firebase.auth();

// ================= SEND OTP =================
window.sendOTP = function () {
  setError(otpErrorEl, "");

  const raw = document.getElementById("mobileNumber").value.trim();
  const clean = raw.replace(/\D/g, "");

  if (!/^[6-9]\d{9}$/.test(clean)) {
    setError(otpErrorEl, "Enter valid 10-digit number");
    return;
  }

  const phone = "+91" + clean;

  // Prevent multiple captcha instances
  if (!window.recaptchaVerifier) {
    window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier(
      "recaptcha-container",
      { size: "normal" }
    );
  }

  setLoading(sendBtn, true, "Sending OTP");

  firebase.auth().signInWithPhoneNumber(phone, window.recaptchaVerifier)
    .then(result => {
      confirmationResult = result;
      // enable OTP inputs
      const otpFields = document.querySelectorAll(".otp-input");
      otpFields.forEach((i, idx) => {
        i.disabled = false;
        i.value = "";
        if (idx === 0) i.focus();
      });

      // Show verify button
      const verifyBtn = document.getElementById("verifyBtn");
      if (verifyBtn) {
        verifyBtn.style.display = "block";
        verifyBtn.scrollIntoView({ behavior: "smooth", block: "center" });
      }

      setLoading(sendBtn, false, "Send OTP");
      setError(otpErrorEl, "OTP sent successfully. Enter the 6-digit code.");
      startResendTimer(30);
    })
    .catch(err => {
      console.error("OTP SEND ERROR:", err);
      setError(otpErrorEl, err.message || "Failed to send OTP");
      setLoading(sendBtn, false, "Send OTP");
    });
};
// ================= VERIFY OTP =================
window.verifyOTP = function () {
  setError(otpErrorEl, "");

  const otpFields = document.querySelectorAll(".otp-input");
  let otp = "";
  otpFields.forEach(i => otp += i.value.trim());

  if (otp.length !== 6) {
    setError(otpErrorEl, "Enter complete 6-digit OTP");
    return;
  }

  if (!confirmationResult) {
    setError(otpErrorEl, "Send OTP first");
    return;
  }

  const verifyBtn = document.getElementById("verifyBtn");
  setLoading(verifyBtn, true, "Verifying OTP");

  confirmationResult.confirm(otp)
    .then(() => {
      setLoading(verifyBtn, false, "Verify OTP");
      setError(otpErrorEl, "");
      
      //  Show success slide immediately
      document.querySelectorAll(".form-slide").forEach(s => s.classList.remove("active"));
      document.getElementById("slide4").classList.add("active");

      // 🧹 Clear captcha instance
      if (window.recaptchaVerifier) {
        window.recaptchaVerifier.clear();
        window.recaptchaVerifier = null;
      }

      //  Send Email in background
      const data = {
        name: document.getElementById("fullName").value,
        phone: document.getElementById("mobileNumber").value,
        email: document.getElementById("emailAddress").value,
        address: document.getElementById("address").value,
        message: document.getElementById("message").value,
        type: document.getElementById("educationType").value,
        course: document.getElementById("courseSelect")?.value || "",
        training_type: document.getElementById("trainingType")?.value || "",
        training_tech: document.getElementById("trainingTech")?.value || "",
        online_course: document.getElementById("onlineCourseSelect")?.value || "",
        service: selectedService || ""
      };

      emailjs.send("service_fall03r", "template_xlpq3cw", data)
        .then(() => console.log("Email sent"))
        .catch(err => console.error("Email failed", err));

      //  Redirect (safe)
      startRedirect();
    })
    .catch(err => {
      console.error("VERIFY ERROR:", err);
      setError(otpErrorEl, "Invalid or expired OTP. Please try again.");
      setLoading(verifyBtn, false, "Verify OTP");
    });
};
// ================= EMAILJS INIT =================
emailjs.init("arbtZPAg8iH0kJRCt");

// ================= OTP INPUTS SETUP =================
const verifyBtn = document.getElementById("verifyBtn");
if (verifyBtn) {
  verifyBtn.style.display = "none";
}

// ================= OTP INPUT AUTO =================
otpInputs.forEach((input, index) => {
    input.addEventListener("input", function () {
        // Allow only digits
        this.value = this.value.replace(/[^0-9]/g, "");
        
        if (this.value.length === 1 && index < otpInputs.length - 1) {
            otpInputs[index + 1].focus();
        }

        // Check if all 6 digits are filled
        const allFilled = Array.from(otpInputs).every(inp => inp.value.trim().length === 1);
        
        if (allFilled && confirmationResult) {
            // Auto-verify OTP when all digits are entered
            setTimeout(() => {
                window.verifyOTP();
            }, 300);
        }
    });

    input.addEventListener("keydown", function (e) {
        if (e.key === "Backspace" && !this.value && index > 0) {
            otpInputs[index - 1].focus();
        }
    });
});

});

function startRedirect() {
  let time = 5;
  const el = document.querySelector(".countdown");

  const t = setInterval(() => {
    time--;
    if (el) el.textContent = time;

    if (time <= 0) {
      clearInterval(t);
      // Use replace to avoid back navigation to form
      window.location.replace("index.html"); // change if needed
    }
  }, 1000);
}
// ===== Buttons & helpers =====
const sendBtn = document.getElementById("sendBtn");
const verifyBtn = document.getElementById("verifyBtn");
const otpErrorEl = document.getElementById("otpError");

function setLoading(btn, isLoading, text = "") {
  if (!btn) return;
  btn.disabled = isLoading;
  btn.innerHTML = isLoading
    ? `<span class="loading"></span> ${text || "Please wait..."}`
    : btn.getAttribute("data-label") || btn.innerText;
}

function setError(el, msg = "") {
  if (!el) return;
  el.innerText = msg;
}

let resendTimer = null;
function startResendTimer(sec = 30) {
  const timerWrap = document.getElementById("timerWrap");
  const resendWrap = document.getElementById("resendWrap");
  const timerEl = document.getElementById("timer");

  let t = sec;
  timerWrap.style.display = "inline";
  resendWrap.style.display = "none";
  timerEl.textContent = t;

  resendTimer = setInterval(() => {
    t--;
    timerEl.textContent = t;
    if (t <= 0) {
      clearInterval(resendTimer);
      timerWrap.style.display = "none";
      resendWrap.style.display = "inline";
    }
  }, 1000);
}

window.resendOTP = function () {
  if (sendBtn) sendBtn.click();
};
