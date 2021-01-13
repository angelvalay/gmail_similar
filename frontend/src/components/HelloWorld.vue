<template>
  <div class="page-container">
    <md-app md-waterfall md-mode="fixed">
      <md-app-toolbar class="md-primary">
        <md-button class="md-icon-button" @click="menuVisible = !menuVisible">
          <md-icon>menu</md-icon>
        </md-button>
        <span class="md-title">Inbox</span>
      </md-app-toolbar>

      <md-app-drawer md-permanent="full" :md-active.sync="menuVisible">
        <md-toolbar class="md-transparent" md-elevation="0">
          Navigation
        </md-toolbar>

        <md-list>
          <md-list-item>
            <md-icon>move_to_inbox</md-icon>
            <span class="md-list-item-text">Inbox</span>
          </md-list-item>

          <md-list-item>
            <md-icon>send</md-icon>
            <span class="md-list-item-text">Sent Mail</span>
          </md-list-item>

          <md-list-item>
            <md-icon>delete</md-icon>
            <span class="md-list-item-text">Trash</span>
          </md-list-item>
        </md-list>
      </md-app-drawer>

      <md-app-content>
        <md-list class="md-triple-line">
          <md-list-item>
            <md-avatar>
              <img src="https://placeimg.com/40/40/people/1" alt="People">
            </md-avatar>

            <div class="md-list-item-text">
              <span>Ali Connors</span>
              <span>Brunch this weekend?</span>
              <p>I'll be in your neighborhood doing errands this week. Do you want to meet?</p>
            </div>

            <md-button class="md-icon-button md-list-action">
              <md-icon class="md-primary">star</md-icon>
            </md-button>
          </md-list-item>

          <md-divider class="md-inset"></md-divider>

          <md-list-item>
            <md-avatar>
              <img src="https://placeimg.com/40/40/people/6" alt="People">
            </md-avatar>

            <div class="md-list-item-text">
              <span>me, Scott, Jennifer</span>
              <span>Summer BBQ</span>
              <p>Wish I could come, but I'm out of town this week. :(</p>
            </div>

            <md-button class="md-icon-button md-list-action">
              <md-icon>star_border</md-icon>
            </md-button>
          </md-list-item>

          <md-divider class="md-inset"></md-divider>

          <md-list-item>
            <md-avatar>
              <img src="https://placeimg.com/40/40/people/5" alt="People">
            </md-avatar>

            <div class="md-list-item-text">
              <span>Sandra Adams</span>
              <span>Oui oui</span>
              <p>Do you have Paris recommendations? Have you visited good places?</p>
            </div>

            <md-button class="md-icon-button md-list-action">
              <md-icon>star_border</md-icon>
            </md-button>
          </md-list-item>

          <md-divider class="md-inset"></md-divider>

          <md-list-item>
            <md-avatar>
              <img src="https://placeimg.com/40/40/people/8" alt="People">
            </md-avatar>

            <div class="md-list-item-text">
              <span>Trevor Hansen</span>
              <span>Order confirmation</span>
              <p>Thank you for your recent order from Amazon</p>
            </div>

            <md-button class="md-icon-button md-list-action">
              <md-icon>star_border</md-icon>
            </md-button>
          </md-list-item>
        </md-list>
      </md-app-content>
    </md-app>
  </div>
</template>

<script>
export default {
  name: 'HelloWorld',
  props: {
    msg: String
  },
  data: () => ({
    menuVisible: true
  })
}
</script>

<style lang="scss" scoped>
.md-app {
  border: 1px solid rgba(#000, .12);
}
.md-app-content{
  padding: 0;
  min-height: 100vh;
}
</style>
