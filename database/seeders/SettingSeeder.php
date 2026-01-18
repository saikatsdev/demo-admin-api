<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $appName = env("APP_NAME");

        Setting::insert([
            [
                "setting_category_id" => 1,
                "key"                 => "title",
                "value"               => $appName,
                "type"                => "input",
                "instruction"         => "এখানে আপনি আপনার ওয়েবসাইটের Branding নাম দিবেন যেটা Dynamically ওয়েবসাইটির বিভিন্ন জায়গায় ূ show করবে।"
            ],
            [
                "setting_category_id" => 1,
                "key"                 => "invoice_number",
                "value"               => 'SKL',
                "type"                => "input",
                "instruction"         => "Invoice er aga j name ta thakbe sheta"
            ],
            [
                "setting_category_id" => 1,
                "key"                 => "is_sub_category_show",
                "value"               => 1,
                "type"                => "switch-button",
                "instruction"         => "sub category display or not"
            ],
            [
                "setting_category_id" => 1,
                "key"                 => "is_sub_sub_category_show",
                "value"               => 1,
                "type"                => "switch-button",
                "instruction"         => "sub sub category display or not display"
            ],
            [
                "setting_category_id" => 1,
                "key"                 => "is_bonus_point_add",
                "value"               => 1,
                "type"                => "switch-button",
                "instruction"         => "আপনি যদি আপনার ওয়েবসাইটে প্রতিটি অর্ডারের উপরে বোনাস পয়েন্ট  দিতে চান তাহলে এই অপশনটি ON করে দেন ।"
            ],
            [
                "setting_category_id" => 1,
                "key"                 => "bonus_point_value",
                "value"               => 1000,
                "type"                => "input",
                "instruction"         => "১ টাকা এর সমপরিমাণ কতগুলো বোনাস পয়েন্ট হবে, তা এখানে নির্ধারণ করে দেন ।"
            ],
            [
                "setting_category_id" => 1,
                "key"                 => "is_login_required",
                "value"               => 0,
                "type"                => "switch-button",
                "instruction"         => "আপনার ওয়েবসাইটে যদি কাস্টমার লগইন ফিচার্স টি রাখতে চান তাহলে, এই সুইচটি অন করে দিন ।"
            ],
            [
                "setting_category_id" => 1,
                "key"                 => "is_customer_login",
                "value"               => 0,
                "type"                => "switch-button",
                "instruction"         => "যদি আপনার ওয়েবসাইটে কাস্টমার কে লগইন করেই অর্ডার করাতে চান তাহলে এই সুইচটি অন করে দিন ।"
            ],
            [
                "setting_category_id" => 1,
                "key"                 => "otp_required_when_staff_login",
                "value"               => 0,
                "type"                => "switch-button",
                "instruction"         => "স্টাফদের লগইন করার সময় যদি ওটিপি দিয়ে ভেরিফাই করাতে চান তাহলে এই সুইচটি অন করে দিন ।"
            ],
            [
                "setting_category_id" => 1,
                "key"                 => "order_locked_duration",
                "value"               => 10,
                "type"                => "input",
                "instruction"         => "অর্ডার পেইজে একজন কত মিনিট অবস্থান করতে পারবে সেইটার সময় নির্ধারন করে দিন ।"
            ],
            [
                "setting_category_id" => 1,
                "key"                 => "frontend_base_url",
                "value"               => "",
                "type"                => "input",
                "instruction"         => "আপনার ওয়েবসাইটের মেইন পাথ বা আপনার ডোমেইনটি এখানে সেটআপ করুন ।"
            ],
            [
                "setting_category_id" => 1,
                "key"                 => "duplicate_order_check_duration",
                "value"               => 12,
                "type"                => "input",
                "instruction"         => "Duplicate order check duration in hours"
            ],
            [
                "setting_category_id" => 1,
                "key"                 => "block_user_message",
                "value"               => "You have been blocked from placing orders. Please contact by 01000000000",
                "type"                => "input",
                "instruction"         => "Message when user have been block"
            ],
            [
                "setting_category_id" => 2,
                "key"                 => "favicon_icon",
                "value"               => "favicon image icon",
                "type"                => "image",
                "instruction"         => "আপনার ওয়েবসাইটের favicon লোগোটি এখানে আপলোড করেন ।"
            ],
            [
                "setting_category_id" => 2,
                "key"                 => "header_logo",
                "value"               => "https://i0.wp.com/servicekey.io/wp-content/uploads/2023/12/Service-Key-Official-Logo-2.png?resize=2048%2C469&ssl=1",
                "type"                => "image",
                "instruction"         => "আপনার ওয়েবসাইটের Main Header লোগোটি এখানে আপলোড করেন ।"
            ],
            [
                "setting_category_id" => 2,
                "key"                 => "footer_logo",
                "value"               => "https://i0.wp.com/servicekey.io/wp-content/uploads/2023/12/Service-Key-Official-Logo-2.png?resize=2048%2C469&ssl=1",
                "type"                => "image",
                "instruction"         => "আপনার ওয়েবসাইটের Footer লোগোটি এখানে আপলোড করেন ।"
            ],
            [
                "setting_category_id" => 3,
                "key"                 => "is_stock_maintain",
                "value"               => 1,
                "type"                => "switch-button",
                "instruction"         => "Product এর Stock Maintain করতে চাইলে Switcher টি ON করে দিন, না চাইলে Switcher টি OFF করে দিন ।"
            ],
            [
                "setting_category_id" => 3,
                "key"                 => "is_stock_maintain_with_direct_product",
                "value"               => 1,
                "type"                => "switch-button",
                "instruction"         => "সরাসরি Product Add কিংবা Product Edit এর সাথে Stock Maintenance করতে চাইলে Switcher টি ON করে দিন । আর যদি Purchase করে Stock Maintenance করতে চান তাহলে Switcher টি OFF করে দিন।"
            ],
            [
                "setting_category_id" => 3,
                "key"                 => "is_negative_stock_allow",
                "value"               => 0,
                "type"                => "switch-button",
                "instruction"         => "Customer can order without current stock"
            ],


            [
                "setting_category_id" => 4,
                "key"                 => "phone_number",
                "value"               => "01780-390707",
                "type"                => "input",
                "instruction"         => "আপনার ওয়েবসাইটে যে ফোন নাম্বারটি ব্যবহার করতে চান সেটি এখানে দিন ।"
            ],
            [
                "setting_category_id" => 4,
                "key"                 => "whatsapp_number",
                "value"               => "01780-390707",
                "type"                => "input",
                "instruction"         => "আপনার ব্যবহৃত হোয়াটসঅ্যাপ নাম্বারটি এখানে দিন ।"
            ],
            [
                "setting_category_id" => 4,
                "key"                 => "messenger_id",
                "value"               => "",
                "type"                => "input",
                "instruction"         => "আপনার ফেসবুক পেইজের আইডি নাম্বারটি এখানে দিন ।"
            ],
            [
                "setting_category_id" => 4,
                "key"                 => "facebook_id",
                "value"               => "",
                "type"                => "input",
                "instruction"         => "আপনার ফেসবুক পেইজের আইডি নাম্বারটি এখানে দিন ।"
            ],
            [
                "setting_category_id" => 4,
                "key"                 => "footer_description",
                "value"               => "$appName is a fashion brand of specially designed ethnic wear like panjabi, pajama, kabli set, koty, sherowani etc.  It also sells fashion accessories like leather wallet, shoes, belts, parces, topi, rumal, perfumes etc.",
                "type"                => "description",
                "instruction"         => "আপনি চাইলে ওয়েবসাইটের Footer এ ব্যবহৃত Logo এর নিচের ডেসক্রিপশনটি এখান থেকে পরিবর্তন করতে পারেন ।"
            ],
            [
                "setting_category_id" => 4,
                "key"                 => "footer_email",
                "value"               => "rameejmd@gmail.com",
                "type"                => "input",
                "instruction"         => "আপনি চাইলে ওয়েবসাইটের Footer এ ব্যবহৃত Email এখান থেকে পরিবর্তন করতে পারেন ।"
            ],
            [
                "setting_category_id" => 4,
                "key"                 => "footer_address",
                "value"               => "Eastern Plaza Shopping Complex Level 3 (Right Side), Shop No. 3/35 Hatirpool, Dhaka 1205, Dhaka, Bangladesh",
                "type"                => "description",
                "instruction"         => "আপনি চাইলে ওয়েবসাইটের Footer এ ব্যবহৃত Address এখান থেকে পরিবর্তন করতে পারেন ।"
            ],
            [
                "setting_category_id" => 1,
                "key"                 => "is_landing_page_maintain",
                "value"               => 1,
                "type"                => "switch-button",
                "instruction"         => "লেন্ডিং পেইজ ফিচার এর জন্য Switcher টি ON করে দিন, না চাইলে Switcher টি OFF করে দিন ।"
            ],
            [
                "setting_category_id" => 4,
                "key"                 => "campaign_section_banner_image",
                "value"               => "https://i0.wp.com/servicekey.io/wp-content/uploads/2023/12/Service-Key-Official-Logo-2.png?resize=2048%2C469&ssl=1",
                "type"                => "image",
                "instruction"         => "Home Page Campaign Section এর জন্য Background Image টি এখানে আপডেট করুন ।"
            ],


            [
                "setting_category_id" => 5,
                "key"                 => "is_use_top_header",
                "value"               => 0,
                "type"                => "switch-button",
                "instruction"         => "আপনার ওয়েবসাইটে ব্যবহৃত Top Header টি যদি ব্যবহার করতে চান তাহলে সুইচটি অন করে দিন ।"
            ],
            [
                "setting_category_id" => 5,
                "key"                 => "top_header_text",
                "value"               => "$appName is a fashion brand of specially designed ethnic wear like panjabi, pajama, kabli set, koty, sherowani etc.  It also sells fashion accessories like leather wallet, shoes, belts, parces, topi, rumal, perfumes etc.",
                "type"                => "input",
                "instruction"         => "আপনার ওয়েবসাইটে ব্যবহৃত Top Header এর Left side এর লেখাটি এখানে পরিবর্তন করুন ।"
            ],
            [
                "setting_category_id" => 5,
                "key"                 => "is_scroll_top_header_text",
                "value"               => 1,
                "type"                => "switch-button",
                "instruction"         => "আপনার ওয়েবসাইটে ব্যবহৃত Top Header এর Left side এর লেখাটি Scroll করে দেখাতে চান তাহলে সুইচটি অন করে দিন ।"
            ],
            [
                "setting_category_id" => 6,
                "key"                 => "primary_color",
                "value"               => "#000000",
                "type"                => "input",
                "instruction"         => "আপনার ওয়েবসাইটের Primary Color টি এখানে দিন ।"
            ],
            [
                "setting_category_id" => 6,
                "key"                 => "secondary_color",
                "value"               => "#DFAC43",
                "type"                => "input",
                "instruction"         => "আপনার ওয়েবসাইটের Secondary color টি এখানে দিন ।"
            ],
            [
                "setting_category_id" => 6,
                "key"                 => "text_color",
                "value"               => "#ffffff",
                "type"                => "input",
                "instruction"         => "আপনার ওয়েবসাইটের text color টি এখানে দিন ।"
            ],
            [
                "setting_category_id" => 6,
                "key"                 => "off_white_background",
                "value"               => "#ececec",
                "type"                => "input",
                "instruction"         => "আপনার ওয়েবসাইটের offWhite background এর জন্য color কোডটি এখানে দিন ।"
            ],
            [
                "setting_category_id" => 6,
                "key"                 => "primary_button_background",
                "value"               => "#DFAC43",
                "type"                => "input",
                "instruction"         => "আপনার ওয়েবসাইটের primary button এর background color কোডটি এখানে দিন ।"
            ],
            [
                "setting_category_id" => 6,
                "key"                 => "primary_button_hover_background",
                "value"               => "#000000",
                "type"                => "input",
                "instruction"         => "আপনার ওয়েবসাইটের primary button কে hover এর background color কোডটি এখানে দিন ।"
            ],
            [
                "setting_category_id" => 6,
                "key"                 => "primary_button_color",
                "value"               => "#ffffff",
                "type"                => "input",
                "instruction"         => "আপনার ওয়েবসাইটের primary button এর color কোডটি এখানে দিন ।"
            ],
            [
                "setting_category_id" => 6,
                "key"                 => "primary_button_hover_color",
                "value"               => "#ffffff",
                "type"                => "input",
                "instruction"         => "আপনার ওয়েবসাইটের primary button এর hover এর color কোডটি এখানে দিন ।"
            ],


            [
                "setting_category_id" => 7,
                "key"                 => "checkout_message",
                "value"               => "আপনার অর্ডারটি সফল ভাবে সম্পন্ন হয়েছে । আমাদের একজন প্রতিনিদি আপনাকে ফোন করে অর্ডারটি ভেরিফাই করবেন।",
                "type"                => "descripiton",
                "instruction"         => "আপনার ওয়েবসাইটের ThankYou Page এর Custom Message পরিবর্তন করতে চাইলে এখানে লিখুন ।"
            ],
            [
                "setting_category_id" => 7,
                "key"                 => "order_success_message",
                "value"               => "আপনার অর্ডারটি সফল ভাবে সম্পন্ন হয়েছে । আমাদের একজন প্রতিনিদি আপনাকে ফোন করে অর্ডারটি ভেরিফাই করবেন।",
                "type"                => "descripiton",
                "instruction"         => "আপনার ওয়েবসাইটের ThankYou Page এর Custom Message পরিবর্তন করতে চাইলে এখানে লিখুন ।"
            ],
        ]);
    }
}
