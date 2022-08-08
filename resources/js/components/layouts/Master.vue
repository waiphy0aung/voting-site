<template>
    <div>
        <Header />
        <Menu />
        <div class="container">
            <div class="row my-3">
                <slot></slot>
            </div>
        </div>
        <Footer />
    </div>
</template>
<script>
import Header from './Header.vue'
import Menu from '../components/Menu.vue'
import Footer from './Footer.vue'
export default {
  components: { Header,Menu,Footer },
}
</script>
