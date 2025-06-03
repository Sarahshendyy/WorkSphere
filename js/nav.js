
window.onscroll = () => {
const winScroll = document.documentElement.scrollTop || document.body.scrollTop;
const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
const scrolled = (winScroll / height) * 100;
document.getElementById("scroll-line").style.width = scrolled + "%";
  };
