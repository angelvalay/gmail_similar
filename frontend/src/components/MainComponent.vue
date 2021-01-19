<template>
  <div class="page-container">
    <md-app md-waterfall md-mode="fixed">
      <!-- barra superior -->
      <md-app-toolbar class="md-primary">
        <md-button class="md-icon-button" @click="menuVisible = !menuVisible">
          <md-icon>menu</md-icon>
        </md-button>
        <h3 class="md-title">{{ this.title }}</h3>
        <div class="md-toolbar-section-end" >
          <md-button class="md-icon-button" @click="reload">
            <md-icon>refresh</md-icon>
          </md-button>
        </div>
      </md-app-toolbar>
      <!-- fin de la barra superior -->

      <!-- inicio de la barra lateral -->
      <md-app-drawer md-persistent="mini" :md-active.sync="menuVisible">
        <md-toolbar class="md-transparent" md-elevation="0">
          <span class="md-title">Menu</span>
          <div class="md-toolbar-section-end">
            <md-button class="md-icon-button md-dense" @click="menuVisible = !menuVisible">
              <md-icon>keyboard_arrow_left</md-icon>
            </md-button>
          </div>
        </md-toolbar>
        <md-list>
          <md-list-item @click="changeOption(0)">
            <md-icon>inbox</md-icon>
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
        <NewEmailComponent @addNewEmail="addNewEmail"></NewEmailComponent>
        <InboxComponent @view="viewEmail" v-show="optionMenuSelected === 0" ref="inboxC"></InboxComponent>
        <SentComponent v-show="optionMenuSelected === 1" ref="sentC"></SentComponent>
        <TrashComponent v-show="optionMenuSelected === 2" ref="trashC"></TrashComponent>

        <ViewEmailComponent v-show="optionMenuSelected === 3" ref="viewC"></ViewEmailComponent>
      </md-app-content>

    </md-app>

  </div>
</template>

<script>
import InboxComponent from "@/components/InboxComponent.vue";
import SentComponent from "@/components/SentComponent";
import TrashComponent from "@/components/TrashComponent.vue";
import NewEmailComponent from "@/components/NewEmailComponent";
import ViewEmailComponent from "@/components/ViewEmailComponent";

export default {
  name: 'MainComponent',
  components:{ViewEmailComponent, InboxComponent, SentComponent, TrashComponent, NewEmailComponent},
  data: () => ({
    menuVisible: true,
    optionMenuSelected:0,
    title: 'Inbox'
  }),
  methods:{
    changeOption(option){
      this.optionMenuSelected = option;
      if (option === 0){
        this.title = 'Inbox';
      }else if(option === 1){
        this.title = 'Sent';
      }else if(option === 2){
        this.title = 'Trash';
      }
    },
    reload(){
      if (this.optionMenuSelected === 0){
        this.$refs.inboxC.getAllEmailsAPI();
      } else if(this.optionMenuSelected === 1){
        this.$refs.sentC.getAllEmailsAPI();
      } else if(this.optionMenuSelected === 2){
        this.$refs.trashC.getEmailsTrashAPI();
      }
    },
    addNewEmail(email){
      if (this.optionMenuSelected === 0)
        this.$refs.sentC.setEmail(email);
    },
    viewEmail(email){
      this.optionMenuSelected = 3;
      this.$refs.viewC.viewEmail(email);
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
  height: calc(100vh - 70px);
}
</style>
