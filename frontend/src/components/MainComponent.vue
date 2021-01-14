<template>
  <div class="page-container">
    <md-app md-waterfall md-mode="fixed">
      <!-- barra superior -->
      <md-app-toolbar class="md-primary">
        <md-button class="md-icon-button" @click="menuVisible = !menuVisible">
          <md-icon>menu</md-icon>
        </md-button>
        <h3 class="md-title" style="flex: 1">{{ this.title }}</h3>
      </md-app-toolbar>
      <!-- fin de la barra superior -->

      <!-- inicio de la barra lateral -->
      <md-app-drawer md-permanent="full" :md-active.sync="menuVisible">
        <md-toolbar class="md-transparent" md-elevation="0">
          <span class="md-title">Menu</span>
        </md-toolbar>
        <md-list>
          <md-list-item @click="changeOption(0)">
            <md-icon>move_to_inbox</md-icon>
            <span class="md-list-item-text">Inbox</span>
          </md-list-item>

          <md-list-item @click="changeOption(1)">
            <md-icon>send</md-icon>
            <span class="md-list-item-text">Sent</span>
          </md-list-item>

          <md-list-item @click="changeOption(2)">
            <md-icon>delete</md-icon>
            <span class="md-list-item-text">Trash</span>
          </md-list-item>
        </md-list>
      </md-app-drawer>
      <!-- fin de la barra -->

      <md-app-content>
        <NewEmailComponent></NewEmailComponent>
        <InboxComponent v-if="optionMenuSelected === 0"></InboxComponent>
        <SentComponent v-else-if="optionMenuSelected === 1"></SentComponent>
        <TrashComponent v-else-if="optionMenuSelected === 2"></TrashComponent>
      </md-app-content>

    </md-app>

  </div>
</template>

<script>
import InboxComponent from "@/components/InboxComponent.vue";
import SentComponent from "@/components/SentComponent";
import TrashComponent from "@/components/TrashComponent.vue";
import NewEmailComponent from "@/components/NewEmailComponent";

export default {
  name: 'MainComponent',
  components:{InboxComponent, SentComponent, TrashComponent, NewEmailComponent},
  data: () => ({
    menuVisible: true,
    optionMenuSelected:0,
    title: 'Inbox'
  }),
  methods:{
    changeOption(option){
      this.optionMenuSelected = option;
      this.menuVisible = false;
      if (option === 0){
        this.title = 'Inbox';
      }else if(option === 1){
        this.title = 'Sent';
      }else if(option === 2){
        this.title = 'Trash';
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.md-app {
  border: 1px solid rgba(#000, .12);
}

.md-drawer{
  max-width: 250px;
}
.md-app-content{
  padding: 0;
  min-height: calc(100vh - 64px);
}
</style>
