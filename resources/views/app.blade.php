@include('layouts.header')
    <script>
        if(localStorage.getItem("auth")){

            const auth = JSON.parse(localStorage.getItem("auth"));
            localStorage.setItem("token",auth.data.token);
        }
    </script>

<div class="loader-container">
    <div class="loader">
        <img src="{{ asset('images/loading.gif') }}" alt="">
    </div>
</div>

<div id="app">
    <router-view></router-view>
    <vue-progress-bar></vue-progress-bar>
</div>

@include('layouts.foot')
