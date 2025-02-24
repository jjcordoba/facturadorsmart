 <style>
#header_bar .header-menu {
    max-height: 300px !important;
    overflow:auto;
    overflow-y: auto;
}
#header_bar .header-menu::-webkit-scrollbar-track
{
	-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.1);
	background-color: #fdfdfd;
}

#header_bar .header-menu::-webkit-scrollbar
{
	width: 6px;
	background-color: #fdfdfd;
}

#header_bar .header-menu::-webkit-scrollbar-thumb
{
	background-color: #0187cc;
}
.header-dropdown a img {
    border-radius: 8px;
    padding: 4px;
}

@media (max-width: 768px) {
    .header-dropdown {
        min-width: 250px !important;
    }
}


.header-menu ul a {
    padding: 3px 6px;
}

.header-menu {
    box-shadow: 0 0 2px rgba(0,0,0,0.1);
    padding: 0 !important;
    border: none;
    /*border-radius:10px;*/
 }

 .header-menu a:hover, .header-menu a:focus {
    color: #0187cc;
    background-color: #f4f4f4;
}

.header-menu ul a {
    text-transform: capitalize !important;
}

.search_input {
    margin-bottom: 0.1rem;
    /* border-radius: 20px !important; */
}
.minWSize{
    min-width: 800px;
}
@media (min-width: 760px) and (max-width: 1260px) {
    .minWSize{
        min-width: 500px;
    }
}
.search_title{

}
.search_price{

}
.search_btn{

}

.search_input:focus {
    background-color: #fff;
    border-color: #fff;
    box-shadow: none;

}

.header-contact span {
    font-weight: normal;
}


 </style>

 <header class="header">

     <div class="header-middle"
     style="padding-bottom: 1rem;padding-top: 1rem;"
     >
        <div class="container">
            <span style="color:black;">
                {{$information->information_contact_address}}
            </span>
        </div>
         <div   class="container">
             <div class="header-left">
                 <a href="{{ route("tenant.ecommerce.index") }}" class="logo" style="max-width: 180px">
                    @if($information->logo)
                        <img 
                        style="max-width: 180px;"
                        src="{{ asset('storage/uploads/logos/'.$information->logo) }}" alt="Logo" />
                    @else
                        <img src="{{asset('logo/tulogo.png')}}" alt="Logo" />
                    @endif
                 </a>
             </div><!-- End .header-left -->

             <div id="header_bar" class="header-center header-dropdowns"
             
             >

                 <div class="header-dropdown minWSize">

                    <input placeholder="Buscar..." type="text" class="search_input form-control form-control-lg" v-model="value" v-on:keyup="autoComplete"
                    style="max-width:none;!important; "
                    />
                     <div class="header-menu">
                         <ul v-if="results.length > 0">
                            <li v-for="result in results">
                                <a :href="'/ecommerce/item/' + result.id" class="d-flex">
                                    <div class="flex-grow-1"><img style="max-width: 80px" :src="result.image_url_small" alt="England flag">
                                    <span class="search_title" style="font-size: 1.0em;"> @{{ result.description }} </span>
                                    </div>
                                    <span class="search_price">@{{result.sale_unit_price}}</span>
                                    {{-- <div class="search_btn btn btn-default">@{{result.sale_unit_price}}</div> --}}
                                </a>
                            </li>
                         </ul>
                     </div><!-- End .header-menu -->
                 </div><!-- End .header-dropown -->


             </div><!-- End .headeer-center -->

             <div class="header-right">
                 <button class="mobile-menu-toggler" type="button">
                     <i class="icon-menu"></i>
                 </button>
                 {{-- <div class="header-contact">
                     <span> Atención al</span>
                     <i class="fab fa-whatsapp"></i> <a href="#"><strong>{{$information->information_contact_phone}}</strong></a>
                 </div> --}}
                 <!-- End .header-contact -->

                @include('ecommerce::layouts.partials_ecommerce.cart_dropdown')

                @include('ecommerce::partials.headers.session')

             </div><!-- End .header-right -->
         </div><!-- End .container -->
     </div><!-- End .header-middle -->

     <div class="header-bottom sticky-header">
        <div class="container d-flex">
            <nav class="main-nav flex-grow-1">

             </nav>
         </div><!-- End .header-bottom -->
     </div><!-- End .header-bottom -->
 </header><!-- End .header -->

 @push('scripts')

 <script type="text/javascript">
     var app = new Vue({
         el: '#header_bar',
         data: {
             value: '',
             suggestions: [],
             resource: 'ecommerce',
             results: [],
         },
         created() {
             this.getItems()
         },
         methods: {
             autoComplete() {
                 if (this.value) {
                    let val = this.value.toUpperCase()
                    this.results = this.suggestions.filter((obj) => {
                         let desc = obj.description.toUpperCase()
                         let internal_id = obj.internal_id ? obj.internal_id.toUpperCase() : ''
                         return desc.includes(val) ||  internal_id.includes(val)
                    })

                 } else {
                     this.results = []
                 }
             },
             getItems() {
                 let contex = this
                 fetch(`/${this.resource}/items_bar`)
                     .then(function (response) {
                         return response.json();
                     })
                     .then(function (myJson) {
                        contex.suggestions = myJson.data
                     });
             },
             suggestionClick(item) {
                 this.results = []
                 this.value = item.description
             }

         }
     })

 </script>

 @endpush
