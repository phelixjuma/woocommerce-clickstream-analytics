"use strict";

let current_user = data.current_user;

let access_key = data.access_key; // access key (from your Insense portal)

let idt_analytics = new IDTECommerceAnalytics(access_key);

// if user exists, we set identification with the user details. Only id is compulsory
if (current_user.data.ID) {
    idt_analytics.identify(current_user.data.ID, current_user.data.display_name, current_user.data.user_email, "");
} else {
    idt_analytics.identify();
}