<template>
    <div>
        <Header />
        <Menu />
        <div class="container">
            <div class="row my-3">
                <slot></slot>
            </div>
        </div>
    </div>
</template>
<script>
import Header from './Header.vue'
import Menu from './Menu.vue';
export default {
  components: { Header , Menu },
}
</script>
