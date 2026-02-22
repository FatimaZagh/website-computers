<link rel="stylesheet"href="css/bootstrap.css">
<meta name="viewport"content="width=device-width,initial-scale=1.0">
<style>
    .row{
       margin-right: 0px;
       margin-left: 0px;
    }
    body{
        padding:0px;
        padding-top:90px;
        margin:0px;
        max-width: 100%;
        overflow-x: hidden;

    }
nav{
    height:70px;
    background: rgba(10, 10, 15, 0.95);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    align-items:center;
    display:flex;
    top:0;
    position:fixed;
    z-index:1000;
    width:100%;
    justify-content:space-between;
    padding:0px 20px 0px 20px;
    box-shadow: 0 2px 20px rgba(0, 0, 0, 0.3);


}
nav ul{
    display:flex;
    list-style:none;
    padding-top:25px;
    margin-right:0px;
    align-items: center;



}
nav ul li a{
    text-decoration:none;
    color: rgba(255, 255, 255, 0.9);
    font-size:16px;
    font-weight:600;
    letter-spacing:0.5px;
    padding:12px 18px;
    border-radius: 8px;
    transition: all 0.3s ease;
    position: relative;

}

nav ul li a:hover{
    text-decoration:none;
    color: #667eea;
    background: rgba(102, 126, 234, 0.1);
    backdrop-filter: blur(10px);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);

}

#acc:hover{
    background: rgba(102, 126, 234, 0.15);
    color: #667eea;
}
nav ul li{
    margin:0px 10px;
}
.logo{
    color: rgba(255, 255, 255, 0.95);
    font-size:28px;
    margin-bottom:0px;
    font-weight:700;
    display: flex;
    align-items: center;
}
.logo:hover{
    color: #667eea;
    text-shadow: 0 0 10px rgba(102, 126, 234, 0.5);
    cursor:pointer;
    transition: all 0.3s ease;
}
nav .menu-btn i{
    color:white;
    font-size:22px;
    cursor:pointer;
    display:none;
}
#click{
    display:none;
}

.underlined{
    border-style:solid;
    border-top:none;
    border-left:none;
    border-right:none;
    border-width:1px;
    padding:2px 2px;


}
.col-sm{

    margin:20px;
    padding:20px;
    padding:2px 2px;
    text-align:center;

    cursor:pointer;

}
/* .col-sm:hover{
    border-style:solid;
    border-radius:10px;

    text-align:center;
    border-width:1px;
    cursor:pointer;
    color:white;
    background-color:black;
    transition:0.2s ease;
} */

.slink:hover{
    text-shadow: 0 0 2px white;
}
#bg{
    font-family: Arial;
  font-size: 150px;
  background-color:white;
  margin-bottom:900px;
  opacity:0.9;
  animation: fadeIn 10s;
}
@keyframes fadeIn {
  0% { opacity: 0; }
  100% { opacity: 1; }
}
@media (max-width:940px)
{


    .text{
        margin-top:0px;
       margin-bottom:0px;
    }
    nav ul{
        position:fixed;
        top:70px;
        left:-100%;
        background: rgba(10, 10, 15, 0.98);
        backdrop-filter: blur(20px);
        height:calc(100vh - 70px);
        width:100%;
        display:block;
        text-align:center;
        margin-top:0px;
        padding-top:50px;
        z-index:999;
        overflow-y:auto;
    }
    nav{
        padding:0px 20px 0px 20px;
        height:70px;
    }
    .logoimg{
        width:10px;
    }
    .restx{font-weight:400;padding-top:100px;padding-bottom:100px;font-size:50px;}
    nav ul li{
        margin:40px 0;

        font-size:20px;

    }
    nav ul li a{
        font-size:20px;
        display:block;
        margin-right:0px;
        padding:15px 20px;
        border-radius:8px;
        margin:10px 20px;
        transition: all 0.3s ease;
    }
    nav .menu-btn{
        position: relative;
        z-index: 1001;
    }
    nav .menu-btn i{
        display:block;
        padding-top:25px;
        color: rgba(255, 255, 255, 0.9);
        cursor: pointer;
    }
    #click:checked ~ul{
        left:0%;
        transition:all 0.3s ease;
    }

    /* Ensure mobile menu doesn't interfere when closed */
    nav ul{
        pointer-events: none;
        visibility: hidden;
    }

    #click:checked ~ul{
        pointer-events: auto;
        visibility: visible;
    }

    /* Prevent body scroll when mobile menu is open */
    #click:checked ~ body{
        overflow: hidden;
    }
img{
       margin-top:0px;
    }
#click:checked ~ .menu-btn i:before{
    content:"\";
}
.container{
    margin-top:-50px;
}

}
.col-sm{
    padding-top:10px;
    padding-bottom:20px;
    border-radius:10px;
}
.col-sm:hover{
    background-color:black;
    border-radius:0px;
    transition:0.3s ease;

    opacity:0.9;
    color:white;
}


#section2{
    margin-top:100px;
}
.buylinks{
    border-style:solid;
    border-color:black;
    background-color:black;
    border-radius:0px;
    padding:10px;
    text-decoration:none;
    color:white;
}
.buylinks:hover{
  text-decoration:none;
  color:black;
  border-radius:0px;
  background-color:white;
  transition:0.3s ease;
  border-color:black;
}
.breakline{
    color:black;
}
.viewmorebtn{
  border-style:solid;padding:10px;border-radius:5px;background:#E63033;text-decoration:none;border-color:#E63033;color:white;
}
.viewmorebtn:hover{
  background-color: #C52b2f;
  transition: 0.3s ease;
  border-color:#C52b2f;
  text-decoration:none;
  color:white;
}
.footer{
  background-color:black;
  color:white;
}
.main{
background-image:url("assets/03.jpg");
background-size:100%;
padding-bottom:100vh;
position:relative;
}




body { margin: 0; }
div#slider { overflow: hidden; }
div#slider figure img { width: 20%; float: left; }
div#slider figure {
  position: relative;
  width: 500%;
  margin: 0;
  left: 0;
  text-align: left;
  font-size: 0;
  animation: 30s slidy infinite;
}



.firstlink{
    color:black;
    background-color:none;
    border-style:solid;
    border-radius:5px;
}
.firstlink:hover{
    text-decoration:none;
    background:black;
    color:white;
    border-color:black;
    border-radius:5px;
    transition: 0.3s ease;
}

@media (max-width:940px)
{


    .firstlink{


    }
    .textin{
        background-color:white;
    }

}
#acc:hover{
        color: #667eea;
        text-shadow: 0 0 10px rgba(102, 126, 234, 0.5);
    }
.logoimg{
    opacity:0.5;
}
.logoimg:hover{
    text-shadow: 0 0 10px white;
    opacity:1;
    transition:0.5s ease;
}
.feedback{
    border-spacing: 20px;
    border-collapse: separate;
}
.table{
        overflow-x:auto;
    }
    .memtble tr td{
        padding:10px;
        text-align:center;
    }
    table  tr th{
      padding:20px;
      background-color:black;
      color:white;
      text-align:center;
    }




.partright{
    padding:20px;
    height:100vh;
    text-align:center;
    padding-top:30vh;
    background-color:black;
    width:50%;
    color:white;
    float:right;
}
.partleft{
    background-color:white;
    width:50%;
    float:left;
}
.sectionimages{
    background-color:black;
    height:100vh;
}
@media (max-width:940px)
{

    .partright{
        width:100%;
        height:100%;
        padding-top:5vh;
        opacity:1;
        z-index:0;
        position:fixed;
        padding:20px;

        background-color:black;

    }
    .sectionimages{
        height:60vh;
    }

    .partleft{

      display:none;

    }
    .maclink{
        display:none;
    }
}
.maclink:hover{
    text-decoration:none;
}
.partleft{
  position:absolute;
  height:80vh;

}
.partleft img {
  position:absolute;
  left:0;
}

/* Images Animation */

@-webkit-keyframes imgFade {
 0% {
   opacity:1;
 }
 17% {
   opacity:1;
 }
 25% {
   opacity:0;
 }
 92% {
   opacity:0;
 }
 100% {
   opacity:1;
 }
}

@-moz-keyframes imgFade {
 0% {
   opacity:1;
 }
 17% {
   opacity:1;
 }
 25% {
   opacity:0;
 }
 92% {
   opacity:0;
 }
 100% {
   opacity:1;
 }
}

@-o-keyframes imgFade {
 0% {
   opacity:1;
 }
 17% {
   opacity:1;
 }
 25% {
   opacity:0;
 }
 92% {
   opacity:0;
 }
 100% {
   opacity:1;
 }
}

@keyframes imgFade {
 0% {
   opacity:1;
 }
 17% {
   opacity:1;
 }
 25% {
   opacity:0;
 }
 92% {
   opacity:0;
 }
 100% {
   opacity:1;
 }
}
.partleft img {
  -webkit-animation-name: imgFade;
  -webkit-animation-timing-function: ease-in-out;
  -webkit-animation-iteration-count: infinite;
  -webkit-animation-duration: 8s;

  -moz-animation-name: imgFade;
  -moz-animation-timing-function: ease-in-out;
  -moz-animation-iteration-count: infinite;
  -moz-animation-duration: 8s;

  -o-animation-name: imgFade;
  -o-animation-timing-function: ease-in-out;
  -o-animation-iteration-count: infinite;
  -o-animation-duration: 8s;

  animation-name: imgFade;
  animation-timing-function: ease-in-out;
  animation-iteration-count: infinite;
  animation-duration: 8s;
}
.partleft img:nth-of-type(1) {
  -webkit-animation-delay: 6s;
  -moz-animation-delay: 6s;
  -o-animation-delay: 6s;
  animation-delay: 6s;
}
.partleft  img:nth-of-type(2) {
  -webkit-animation-delay: 4s;
  -moz-animation-delay: 4s;
  -o-animation-delay: 4s;
  animation-delay: 4s;
}
.partleft img:nth-of-type(3) {
  -webkit-animation-delay: 2s;
  -moz-animation-delay: 2s;
  -o-animation-delay: 2s;
  animation-delay: 2s;
}
.partleft img:nth-of-type(4) {
  -webkit-animation-delay: 0;
  -moz-animation-delay: 0;
  -o-animation-delay: 0;
  animation-delay: 0;
}

/* Recently Viewed Products - Elegant Horizontal Strip */
.recently-viewed-strip {
    background: #fff;
    border-top: 1px solid #e9ecef;
    border-bottom: 1px solid #e9ecef;
    padding: 25px 0;
    margin: 40px 0;
}

.strip-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.strip-title i {
    color: #6c757d;
    margin-right: 8px;
}

.clear-all-btn {
    background: #dc3545;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 0.85rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.clear-all-btn:hover {
    background: #c82333;
}

.products-scroll-container {
    overflow-x: auto;
    overflow-y: hidden;
    padding-bottom: 10px;
}

.products-scroll {
    display: flex;
    gap: 15px;
    min-width: min-content;
}

.recently-viewed-card {
    flex: 0 0 180px;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 12px;
    text-align: center;
    position: relative;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    cursor: pointer;
}

.recently-viewed-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    border-color: #007bff;
}

.recently-viewed-card img {
    width: 100%;
    height: 100px;
    object-fit: contain;
    margin-bottom: 10px;
}

.recently-viewed-card .product-name {
    font-size: 0.9rem;
    font-weight: 500;
    color: #495057;
    margin-bottom: 8px;
    line-height: 1.3;
    height: 2.6em;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.recently-viewed-card .product-price {
    font-size: 0.85rem;
    margin-bottom: 8px;
}

.recently-viewed-card .original-price {
    color: #6c757d;
    text-decoration: line-through;
    font-size: 0.8rem;
}

.recently-viewed-card .current-price {
    color: #28a745;
    font-weight: 600;
    margin-left: 5px;
}

.recently-viewed-card .discount-badge {
    color: #dc3545;
    font-size: 0.75rem;
    margin-left: 5px;
}

.recently-viewed-card .view-btn {
    background: #007bff;
    color: white;
    border: none;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    text-decoration: none;
    display: inline-block;
    transition: background-color 0.3s ease;
}

.recently-viewed-card .view-btn:hover {
    background: #0056b3;
    color: white;
    text-decoration: none;
}

.recently-viewed-remove-btn {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 20px;
    height: 20px;
    background: rgba(220, 53, 69, 0.8);
    color: white;
    border: none;
    border-radius: 50%;
    font-size: 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.3s ease;
    z-index: 10;
}

.recently-viewed-remove-btn:hover {
    background: rgba(220, 53, 69, 1);
}

/* Scrollbar styling for webkit browsers */
.products-scroll-container::-webkit-scrollbar {
    height: 6px;
}

.products-scroll-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.products-scroll-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.products-scroll-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .recently-viewed-card {
        flex: 0 0 150px;
    }

    .strip-title {
        font-size: 1.1rem;
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }

    .clear-all-btn {
        align-self: flex-end;
    }
}

</style>
<script src="https://kit.fontawesome.com/a076d05399.js">
    </script>