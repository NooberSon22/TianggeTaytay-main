const storeName = document.querySelector(".store-name");
const storeLogo = document.querySelector(".store-logo");
const productCount = document.querySelector(".product-count");
const storeJoinDate = document.querySelector(".store-join-date");
const storeContact = document.querySelector(".store-contact");
const storeEmail = document.querySelector(".store-email");
const storeStall = document.querySelector(".store-stall");
const storeDescription = document.querySelector(".store-description-text");
const storename = new URLSearchParams(window.location.search).get("storename");
const shopee = document.querySelector(".shopee");
const accounts = document.querySelector(".accounts");
const name = document.querySelector(".name");
let storeid = "";

const URL = "http://localhost:3000";

function formatDate(dateString) {
  const dateObject = new Date(dateString);
  const options = { year: "numeric", month: "short", day: "numeric" };
  return dateObject.toLocaleDateString("en-US", options);
}

function formatPhoneNumber(number) {
  if (number.startsWith("0")) {
    return "+63 " + number.slice(1);
  }
  return number;
}

const displayAccounts = (accountsData) => {
  accountsData.forEach((account) => {
    const div = document.createElement("div");
    div.className = "account";
    const img = document.createElement("img");
    img.src = `data:image/png;base64,${account.img}`;
    img.loading = "lazy";
    div.appendChild(img);

    div.addEventListener("click", () => {
      window.open(account.link, "_blank");
    });

    accounts.appendChild(div);
  });
};

const fetchStoreInfo = async () => {
  try {
    const response = await fetch(
      `${URL}/server/store.php?storename=${storename}`
    );

    if (!response.ok) {
      throw new Error("Failed to fetch store info");
    }

    const { data } = await response.json();
    const storeInfo = data[0];

    storeid = storeInfo.storeid;
    storeName.innerHTML = storeInfo.storename;
    storeLogo.src = `data:image/png;base64,${storeInfo.img}`;
    productCount.innerHTML = storeInfo.product_count;
    storeJoinDate.innerHTML = formatDate(storeInfo.date_joined);
    storeContact.innerHTML = formatPhoneNumber(storeInfo.store_contact);
    storeEmail.innerHTML = storeInfo.store_email;
    storeStall.textContent = storeInfo.stall_numbers;
    name.textContent = storeInfo.storename.toUpperCase();
    storeDescription.innerHTML = storeInfo.description;

    displayAccounts(storeInfo.accounts);
  } catch (error) {
    console.error("Error fetching store info:", error);
  }
};

const renderPage = async () => {
  await fetchStoreInfo();
  await renderUI(storename);
};

window.addEventListener("load", renderPage);
