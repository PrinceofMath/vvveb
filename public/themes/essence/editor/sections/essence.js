/*
Copyright 2017 Ziadin Givan

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

   http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.

https://github.com/givanz/Vvvebjs
*/

Vvveb.SectionsGroup['Essence'] =
["essence/hero1", "essence/banners1", "essence/cta1", "essence/products1", "essence/brands1", "essence/footer1"];

Vvveb.Sections.add("essence/hero1", {
    name: "Main hero",
	dragHtml: '<img src="' + Vvveb.baseUrl + 'icons/product.png">',        
    image: Vvveb.themeBaseUrl + "/editor/sections/hero1-thumb.png",
    html:`
<section class="welcome_area bg-img background-overlay" style="background-image: url(img/bg-img/bg-1.jpg);" id="welcome-area">
        <div class="container h-100">
            <div class="row h-100 align-items-center">
                <div class="col-12">
                    <div class="hero-content">
                        <h6>asoss</h6>
                        <h2>New Collection</h2>
                        <a href="#" class="btn essence-btn">view collection</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
`,
});


Vvveb.Sections.add("essence/banners1", {
    name: "Category banners",
	dragHtml: '<img src="' + Vvveb.baseUrl + 'icons/image.svg">',        
    image: Vvveb.themeBaseUrl + "/editor/sections/banners1-thumb.png",
    html:`
<section class="top_category_area section-padding-80 clearfix" id="top-category">
	<div class="container">
		<div class="row justify-content-center">
			<!-- Single Catagory -->
			<div class="col-12 col-sm-6 col-md-4">
				 <a href="" data-v-url="category/category?category_id=10">
				<div class="single_category_area d-flex align-items-center justify-content-center bg-img" style="background-image: url(img/bg-img/bg-2.jpg);">
					<div class="category-content">
					   <span>Clothing</span>
					</div>
				</div>
				</a>
			</div>
			<!-- Single Catagory -->
			<div class="col-12 col-sm-6 col-md-4">
				 <a href="" data-v-url="category/category?category_id=10">
				<div class="single_category_area d-flex align-items-center justify-content-center bg-img" style="background-image: url(img/bg-img/bg-3.jpg);">
					<div class="category-content">
					   <span>Shoes</span>
					</div>
				</div>
				</a>
			</div>
			<!-- Single Catagory -->
			<div class="col-12 col-sm-6 col-md-4">
				 <a href="" data-v-url="category/category?category_id=10">
				<div class="single_category_area d-flex align-items-center justify-content-center bg-img" style="background-image: url(img/bg-img/bg-4.jpg);">
					<div class="category-content">
					   <span>Accessories</span>
					</div>
				</div>
				</a>
			</div>
		</div>
	</div>
</section>
`,
});



Vvveb.Sections.add("essence/cta1", {
    name: "Global sale call to action",
	dragHtml: '<img src="' + Vvveb.baseUrl + 'icons/image.svg">',        
    image: Vvveb.themeBaseUrl + "/editor/sections/cta1-thumb.png",
    html:`
<section class="cta-area" id="cta-area">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="cta-content bg-img background-overlay" style="background-image: url(img/bg-img/bg-5.jpg);">
					<div class="h-100 d-flex align-items-center justify-content-end">
						<div class="cta--text">
							<h6>-60%</h6>
							<h2>Global Sale</h2>
							<a href="#" class="btn essence-btn">Buy Now</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
`,
});

Vvveb.Sections.add("essence/brands1", {
    name: "Brands list",
	dragHtml: '<img src="' + Vvveb.baseUrl + 'icons/image.svg">',        
    image: Vvveb.themeBaseUrl + "/editor/sections/brands1-thumb.png",
    html:`
<section class="brands-area d-flex align-items-center justify-content-between" data-v-components-manufacturer="" id="brands-area">
	<!-- Brand Logo -->
	<div class="single-brands-logo" data-v-manufacturer="">
		<img src="img/core-img/brand1.png" alt="" data-v-img="">
	</div>
	<!-- Brand Logo -->
	<div class="single-brands-logo" data-v-manufacturer="">
		<img src="img/core-img/brand2.png" alt="" data-v-img="">
	</div>
	<!-- Brand Logo -->
	<div class="single-brands-logo" data-v-manufacturer=""> 
		<img src="img/core-img/brand3.png" alt="" data-v-img="">
	</div>
	<!-- Brand Logo -->
	<div class="single-brands-logo" data-v-manufacturer="">
		<img src="img/core-img/brand4.png" alt="" data-v-img="">
	</div>
	<!-- Brand Logo -->
	<div class="single-brands-logo" data-v-manufacturer="">
		<img src="img/core-img/brand5.png" alt="" data-v-img="">
	</div>
	<!-- Brand Logo -->
	<div class="single-brands-logo" data-v-manufacturer="">
		<img src="img/core-img/brand6.png" alt="" data-v-img="">
	</div>
</section>
`,
});

Vvveb.Sections.add("essence/footer1", {
    name: "Black footer",
	dragHtml: '<img src="' + Vvveb.baseUrl + 'icons/image.svg">',        
    image: Vvveb.themeBaseUrl + "/editor/sections/footer1-thumb.png",
    html:`
<footer class="footer_area clearfix" id="footer">
	<div class="container">
		<div class="row">
			<!-- Single Widget Area -->
			<div class="col-12 col-md-6">
				<div class="single_widget_area d-flex mb-30">
					<!-- Logo -->
					<div class="footer-logo me-50">
						<a href="#"><img src="img/core-img/logo2.png" alt=""></a>
					</div>
					<!-- Footer Menu -->
					<div class="footer_menu">
						<ul>
							<li><a href="/shop" data-v-url="product/index">Shop</a></li>
							<li><a href="" data-v-url="content/index/index">Blog</a></li>
							<li><a href="/page/contact" data-v-url="content/page/index" data-v-url-params="{&quot;slug&quot;:&quot;contact&quot;}">Contact</a></li>
						</ul>
					</div>
				</div>
			</div>
			<!-- Single Widget Area -->
			<div class="col-12 col-md-6">
				<div class="single_widget_area mb-30">
					<ul class="footer_widget_menu">
								<li><a href="/page/contact" data-v-url="content/page/index" data-v-url-params="{&quot;slug&quot;:&quot;contact&quot;}" data-translate="">Order status</a></li>
								<li><a href="/page/payment-options" data-v-url="content/page/index" data-v-url-params="{&quot;slug&quot;:&quot;payment-options&quot;}" data-translate="">Payment options</a></li>
								<li><a href="/page/shipping-delivery" data-v-url="content/page/index" data-v-url-params="{&quot;slug&quot;:&quot;shipping-delivery&quot;}" data-translate="">Shipping &amp; Delivery</a></li>
								<li><a href="/page/guides" data-v-url="content/page/index" data-v-url-params="{&quot;slug&quot;:&quot;guides&quot;}" data-translate="">Guides</a></li>
								<li><a href="/page/term-use" data-v-url="content/page/index" data-v-url-params="{&quot;slug&quot;:&quot;term-use&quot;}" data-translate="">Term of use</a></li>
								<li><a href="/page/privacy-policy" data-v-url="content/page/index" data-v-url-params="{&quot;slug&quot;:&quot;privacy-policy&quot;}" data-translate="">Privacy Policy</a></li>
					</ul>
				</div>
			</div>
		</div>

		<div class="row align-items-end">
			<!-- Single Widget Area -->
			<div class="col-12 col-md-6">
				<div class="single_widget_area">
					<div class="footer_heading mb-30">
						<h6>Subscribe</h6>
					</div>
					<div class="subscribtion_form">
						<form action="#" method="post">
							<input type="email" name="mail" class="mail" placeholder="Your email here">
							<button type="submit" class="submit"><i class="fa fa-long-arrow-right" aria-hidden="true"></i></button>
						</form>
					</div>
				</div>
			</div>
			<!-- Single Widget Area -->
			<div class="col-12 col-md-6">
				<div class="single_widget_area">
					<div class="footer_social_area">
						<a href="#" data-toggle="tooltip" data-placement="top" title="" data-original-title="Facebook"><i class="fa fa-facebook" aria-hidden="true"></i></a>
						<a href="#" data-toggle="tooltip" data-placement="top" title="" data-original-title="Instagram"><i class="fa fa-instagram" aria-hidden="true"></i></a>
						<a href="#" data-toggle="tooltip" data-placement="top" title="" data-original-title="Twitter"><i class="fa fa-twitter" aria-hidden="true"></i></a>
						<a href="#" data-toggle="tooltip" data-placement="top" title="" data-original-title="Pinterest"><i class="fa fa-pinterest" aria-hidden="true"></i></a>
						<a href="#" data-toggle="tooltip" data-placement="top" title="" data-original-title="Youtube"><i class="fa fa-youtube-play" aria-hidden="true"></i></a>
					</div>
				</div>
			</div>
		</div>

		<div class="row mt-5">
			<div class="col-md-12 text-center">
				<p>
					<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
Copyright ©<script>document.write(new Date().getFullYear());</script>2020 All rights reserved | This template is made with <i class="fa fa-heart-o" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->

					Powered by <a href="https://www.vvveb.com" target="_blank">Vvveb</a>	
				</p>
			</div>
		</div>

	</div>
</footer>
`,
});
