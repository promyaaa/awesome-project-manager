<style>
    #app {
        font-size: 15px;
        margin-top: 20px;
    }
    .center {
        margin: auto;
        width: 70%;
        border: 1px solid #eee;
        padding: 10px;
        /*background-color: #fff;*/
        border-radius: 3px;
    }
    .home-nav {
        margin-bottom: 20px !important;
    }
    .common-nav {
        padding: 0px 10px;
    }

    .common-nav a{
        color: #5d5d5d;
        font-size: 16px;
        text-decoration: none;
    }

    .common-nav a:hover{
        text-decoration: none;
        color: #0073aa;
    }
    span.is-active a{
        color: #0073aa;
    }

    .common-nav a i{
        color: #cacaca;
        margin-right: 3px;
    }

    .fade-enter-active {
        transition: opacity 1s
    }
    .fade-leave-active {
        opacity : 0;
    }
    .fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
        opacity: 0
    }
</style>

<div class="wrap" id="fusion-pm">
    <div id="app">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center home-nav">
                    <router-link to="/" exact tag="span" class="link-style common-nav">
                        <i class="fa fa-home" aria-hidden="true" style="font-size: 16px;"></i>
                        <a> Home </a>
                    </router-link>
                </div>

            </div>
        </div>
        <div>
            <transition name="fade">
                <router-view></router-view>
            </transition>
        </div>
    </div>
</div>
