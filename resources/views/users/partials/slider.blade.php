 <!-- Hero slider -->
 <section class="hero" data-hero-slider data-testid="hero-slider" style="width:100%;font-family:inherit">

     <div style="position:relative;width:100%;min-height:88vh;overflow:hidden">

         <div id="hero-track" style="display:flex;width:300%;transition:transform 0.5s ease">
             <!-- Slide 1 — Royal Series (trophy cups) -->
             <div style="width:33.333%;position:relative;min-height:88vh;overflow:hidden">
                 <div 
                    style="position:absolute;inset:0;background-image:url('https://images.pexels.com/photos/7005034/pexels-photo-7005034.jpeg?auto=compress&cs=tinysrgb&w=2000');background-size:cover;background-position:center">
                </div>
                 <div
                     style="position:absolute;inset:0;background:linear-gradient(to right,rgba(0,0,0,0.72) 55%,rgba(0,0,0,0.1))">
                 </div>
                 <div
                     style="position:relative;z-index:1;padding:clamp(2rem,6vw,5rem) clamp(1.5rem,5vw,4rem);max-width:660px;display:flex;flex-direction:column;justify-content:center;min-height:88vh">
                     <div
                         style="font-size:11px;letter-spacing:2.5px;text-transform:uppercase;color:rgba(255,255,255,0.72);margin-bottom:14px">
                         Royal Series · 2026</div>
                     <h1
                         style="font-size:clamp(1.8rem,5vw,3.2rem);font-weight:500;color:#fff;line-height:1.15;margin:0 0 16px">
                         Trophies that <em>celebrate</em><br>more than a moment.</h1>
                     <p
                         style="font-size:clamp(14px,2.2vw,17px);color:rgba(255,255,255,0.82);margin:0 0 28px;line-height:1.65;max-width:480px">
                         Hand-finished cups, crystal awards and corporate mementos — engraved, packed and delivered
                         across India.</p>
                     <div style="display:flex;flex-wrap:wrap;gap:10px">
                         <a href="{{ route('contact') }}" data-testid="hero-shop-btn"
                             style="display:inline-flex;align-items:center;gap:8px;padding:12px 24px;background:#fff;color:#111;border-radius:8px;font-size:15px;font-weight:500;text-decoration:none">Contact Us <span>→</span></a>
                         <a href="{{ route('about') }}"
                             style="display:inline-flex;align-items:center;padding:12px 24px;border:1.5px solid rgba(255,255,255,0.65);color:#fff;border-radius:8px;font-size:15px;font-weight:500;text-decoration:none">Our
                             story</a>
                     </div>
                 </div>
             </div>

             <!-- Slide 2 — Cut Glass -->
             <div style="width:33.333%;position:relative;min-height:88vh;overflow:hidden">
                 <div
                     style="position:absolute;inset:0;background-image:url('https://images.pexels.com/photos/262481/pexels-photo-262481.jpeg?auto=compress&cs=tinysrgb&w=2000');background-size:cover;background-position:center">
                 </div>
                 <div
                     style="position:absolute;inset:0;background:linear-gradient(to right,rgba(0,0,0,0.70) 55%,rgba(0,0,0,0.1))">
                 </div>
                 <div
                     style="position:relative;z-index:1;padding:clamp(2rem,6vw,5rem) clamp(1.5rem,5vw,4rem);max-width:660px;display:flex;flex-direction:column;justify-content:center;min-height:88vh">
                     <div
                         style="font-size:11px;letter-spacing:2.5px;text-transform:uppercase;color:rgba(255,255,255,0.72);margin-bottom:14px">
                         Cut Glass · Limited</div>
                     <h1
                         style="font-size:clamp(1.8rem,5vw,3.2rem);font-weight:500;color:#fff;line-height:1.15;margin:0 0 16px">
                         Crystal awards, <em>etched</em><br>with your story.</h1>
                     <p
                         style="font-size:clamp(14px,2.2vw,17px);color:rgba(255,255,255,0.82);margin:0 0 28px;line-height:1.65;max-width:480px">
                         Premium optical-grade glass, laser-engraved inside the form. Perfect for milestones that
                         deserve permanence.</p>
                     <div style="display:flex;flex-wrap:wrap;gap:10px">
                         <a href="{{ route('contact') }}"
                             style="display:inline-flex;align-items:center;padding:12px 24px;background:#fff;color:#111;border-radius:8px;font-size:15px;font-weight:500;text-decoration:none">Discover
                             Cut Glass →</a>
                     </div>
                 </div>
             </div>

             <!-- Slide 3 — Medals / Bulk -->
             <div style="width:33.333%;position:relative;min-height:88vh;overflow:hidden">
                 <div
                     style="position:absolute;inset:0;background-image:url('https://images.pexels.com/photos/6250940/pexels-photo-6250940.jpeg?auto=compress&cs=tinysrgb&w=2000');background-size:cover;background-position:center">
                 </div>
                 <div
                     style="position:absolute;inset:0;background:linear-gradient(to right,rgba(0,0,0,0.70) 55%,rgba(0,0,0,0.1))">
                 </div>
                 <div
                     style="position:relative;z-index:1;padding:clamp(2rem,6vw,5rem) clamp(1.5rem,5vw,4rem);max-width:660px;display:flex;flex-direction:column;justify-content:center;min-height:88vh">
                     <div
                         style="font-size:11px;letter-spacing:2.5px;text-transform:uppercase;color:rgba(255,255,255,0.72);margin-bottom:14px">
                         Bulk · Schools · Corporates</div>
                     <h1
                         style="font-size:clamp(1.8rem,5vw,3.2rem);font-weight:500;color:#fff;line-height:1.15;margin:0 0 16px">
                         Order <em>500+ medals</em>,<br>with engraving on us.</h1>
                     <p
                         style="font-size:clamp(14px,2.2vw,17px);color:rgba(255,255,255,0.82);margin:0 0 28px;line-height:1.65;max-width:480px">
                         Sports days, marathons, annual awards. Free design proofs, 7-day delivery, pan-India
                         shipping.</p>
                     <div style="display:flex;flex-wrap:wrap;gap:10px">
                         <a href="{{ route('register') }}"
                             style="display:inline-flex;align-items:center;padding:12px 24px;background:#fff;color:#111;border-radius:8px;font-size:15px;font-weight:500;text-decoration:none">Request
                             a quote →</a>
                     </div>
                 </div>
             </div>

         </div>

         <!-- Prev / Next arrows -->
         <button onclick="heroSlide(-1)" aria-label="Previous slide"
             style="position:absolute;left:16px;top:50%;transform:translateY(-50%);z-index:10;background:rgba(255,255,255,0.15);border:1.5px solid rgba(255,255,255,0.45);color:#fff;width:44px;height:44px;border-radius:50%;cursor:pointer;font-size:22px;display:flex;align-items:center;justify-content:center;-webkit-backdrop-filter:blur(4px);backdrop-filter:blur(4px)">‹</button>
         <button onclick="heroSlide(1)" aria-label="Next slide"
             style="position:absolute;right:16px;top:50%;transform:translateY(-50%);z-index:10;background:rgba(255,255,255,0.15);border:1.5px solid rgba(255,255,255,0.45);color:#fff;width:44px;height:44px;border-radius:50%;cursor:pointer;font-size:22px;display:flex;align-items:center;justify-content:center;-webkit-backdrop-filter:blur(4px);backdrop-filter:blur(4px)">›</button>

         <!-- Pagination dots -->
         <div id="hero-dots"
             style="position:absolute;bottom:20px;left:50%;transform:translateX(-50%);display:flex;gap:10px;z-index:10">
             <button onclick="heroGoTo(0)" aria-label="Slide 1"
                 style="width:8px;height:8px;border-radius:50%;border:none;background:#fff;opacity:1;cursor:pointer;padding:0;transition:opacity 0.3s"></button>
             <button onclick="heroGoTo(1)" aria-label="Slide 2"
                 style="width:8px;height:8px;border-radius:50%;border:none;background:#fff;opacity:0.38;cursor:pointer;padding:0;transition:opacity 0.3s"></button>
             <button onclick="heroGoTo(2)" aria-label="Slide 3"
                 style="width:8px;height:8px;border-radius:50%;border:none;background:#fff;opacity:0.38;cursor:pointer;padding:0;transition:opacity 0.3s"></button>
         </div>

     </div>

 </section>

 <script>
     (function() {
         var cur = 0,
             total = 3,
             timer;
         var track = document.getElementById('hero-track');
         var dots = document.getElementById('hero-dots').querySelectorAll('button');

         function updateDots() {
             dots.forEach(function(d, i) {
                 d.style.opacity = i === cur ? '1' : '0.38';
             });
         }
         window.heroGoTo = function(n) {
             cur = n;
             track.style.transform = 'translateX(-' + ((100 / 3) * cur) + '%)';
             updateDots();
         }
         window.heroSlide = function(dir) {
             heroGoTo((cur + dir + total) % total);
         }

         function start() {
             timer = setInterval(function() {
                 heroSlide(1);
             }, 5000);
         }

         function stop() {
             clearInterval(timer);
         }
         var section = track.parentElement;
         section.addEventListener('mouseenter', stop);
         section.addEventListener('mouseleave', start);
         var tx = 0;
         track.addEventListener('touchstart', function(e) {
             tx = e.touches[0].clientX;
             stop();
         }, {
             passive: true
         });
         track.addEventListener('touchend', function(e) {
             var dx = e.changedTouches[0].clientX - tx;
             if (Math.abs(dx) > 40) heroSlide(dx < 0 ? 1 : -1);
             start();
         }, {
             passive: true
         });
         start();
     })();
 </script>
