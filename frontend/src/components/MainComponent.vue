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
          <md-button v-if="!selected" class="md-icon-button" @click="reload">
            <md-icon>refresh</md-icon>
          </md-button>
          <div v-else>
            <md-button v-if="optionMenuSelected === 0" class="md-icon-button" >
              <md-icon>grade</md-icon>
            </md-button>
            <md-button v-if="optionMenuSelected === 0" class="md-icon-button" >
              <md-icon>delete</md-icon>
            </md-button>
            <md-button v-if="optionMenuSelected === 2" class="md-icon-button" >
              <md-icon>restore_from_trash</md-icon>
            </md-button>
            <md-button v-if="optionMenuSelected === 2" class="md-icon-button" >
              <md-icon>delete_forever</md-icon>
            </md-button>
            <md-button class="md-icon-button">
              <md-icon>mark_email_read</md-icon>
            </md-button>
          </div>

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
        <InboxComponent :loaded="loaded" :mails="inbox" :selected="selected" @view="viewEmail" v-show="optionMenuSelected === 0" ref="inboxC"></InboxComponent>
        <SentComponent :loaded="loaded" :mails="sent" :selected="selected" v-show="optionMenuSelected === 1" ref="sentC"></SentComponent>
        <TrashComponent :loaded="loaded" :mails="trash" :selected="selected" v-show="optionMenuSelected === 2" ref="trashC"></TrashComponent>
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
import moment from "moment";

export default {
  name: 'MainComponent',
  components:{ViewEmailComponent, InboxComponent, SentComponent, TrashComponent, NewEmailComponent},
  data: () => ({
    menuVisible: true,
    optionMenuSelected:0,
    title: 'Inbox',
    mails:[],
    loaded:false
  }),
  computed:{
    selected() {
      return this.mails.filter(x=>x.is_selected).length;
    },
    inbox(){
      return this.mails.filter(x=>x.mail_to === null && !x.is_deleted);
    },
    sent(){
      return this.mails.filter(x=>x.mail_to !== null && !x.is_deleted);
    },
    trash(){
      return this.mails.filter(x => x.is_deleted);
    },
  },
  mounted() {
    this.getAllEmailsAPI();
  },
  methods:{
    getMail(mail){
      return mail.mail_from == null ? mail.mail_to : mail.mail_from;
    },

    addExtraProperties(mail){
      mail.is_selected = false;
      mail.first_letter = this.getMail(mail).substring(0,1).toUpperCase();
      mail.created_from_now = moment(mail.created_at).fromNow();
    },

    getAllEmailsAPI(){
      this.loaded = false;
      this.axios.get('http://localhost:8000/mails').then((response)=>{
        let data = response.data;
        for (let i = 0; i < data.length; i++) {
          this.addExtraProperties(data[i]);
        }
        this.mails = data;
      }).catch((error)=>{
        console.log(error);
      }).then(()=>{
        this.loaded = true;
      })
    },
    markAsImportantAPI(index = null){
      this.loaded = false;
      let data = {
        id_mails:[],
        is_important:true
      };
      if (index == null){
        this.mails.filter(mail=>mail.is_selected).forEach(mail=>{
          data.id_mails.push(mail.id);
        });
      }
      else{
        data.id_mails.push(this.mails[index].id);
        data.is_important = !this.mails[index].is_important;
      }

      this.markAsImportant = data.is_important;

      this.axios.put('http://localhost:8000/mails/mark',data).then(()=>{
        this.showMarkedSuccessful = true;

        if (index!=null)
          this.mails[index].is_important = !this.mails[index].is_important;

        this.mails.forEach(mail=>{
          if (mail.is_selected)
            mail.is_important = true;
          mail.is_selected = false;
        });
      }).catch(error=>{
        console.log(error);
      }).then(()=>{
        this.loaded = true;
      });
    },
    sentToTrashAPI(){
      let data = {
        id_mails:[],
        is_deleted:true
      };
      this.loaded = false;
      this.mails.filter(mail=>mail.is_selected).forEach(mail=>{
        data.id_mails.push(mail.id);
      });
      this.axios.put('http://localhost:8000/mails/trash',data).then(()=>{
        this.showTrashSuccessful = true;
        let lastIndexSelected = 0;
        while(lastIndexSelected > -1){
          lastIndexSelected = this.mails.findIndex(mail=>mail.is_selected);
          if (lastIndexSelected === -1)
            break;
          this.mails.splice(lastIndexSelected,1);
        }
      }).catch(error=>{
        console.log(error);
      }).then(()=>{
        this.loaded = true;
      });
    },
    markAsReadAPI(index = null){
      this.loaded = false;
      let data = {
        id_mails:[]
      };
      if (index == null){
        this.mails.filter(mail=>mail.is_selected).forEach(mail=>{
          data.id_mails.push(mail.id);
        });
      }
      else{
        data.id_mails.push(this.mails[index].id);
      }


      this.axios.put('http://localhost:8000/mails/read',data).then(()=>{
        //this.showMarkedSuccessful = true;

        if (index!=null)
          this.mails[index].is_read = !this.mails[index].is_read;

        this.mails.forEach(mail=>{
          if (mail.is_selected)
            mail.is_read = true;
          mail.is_selected = false;
        });
      }).catch(error=>{
        console.log(error);
      }).then(()=>{
        this.loaded = true;
      });
    },
    deleteMailsSelectedAPI(){
      let data = {
        id_mails:[]
      };
      this.mails.filter(mail=>mail.is_selected).forEach(mail=>{
        data.id_mails.push(mail.id);
      });
      this.axios.put('http://localhost:8000/mails/delete',data).then(()=>{

        this.showSuccessfulMessageDelete = true;

        let lastIndexSelected = 0;
        while(lastIndexSelected > -1){
          lastIndexSelected = this.mails.findIndex(mail=>mail.is_selected);
          if (lastIndexSelected === -1)
            break;
          this.mails.splice(lastIndexSelected,1);
        }
      }).catch(error=>{
        console.log(error);
      }).then(()=>{

      });
    },
    sendToInboxAPI(){
      let data = {
        id_mails:[],
        is_deleted:false
      };
      this.mails.filter(mail=>mail.is_selected).forEach(mail=>{
        data.id_mails.push(mail.id);
      });
      this.axios.put('http://localhost:8000/mails/trash',data).then(()=>{
        this.showSentInboxSuccessful = true;
        let lastIndexSelected = 0;
        while(lastIndexSelected > -1){
          lastIndexSelected = this.mails.findIndex(mail=>mail.is_selected);
          if (lastIndexSelected === -1)
            break;
          this.mails.splice(lastIndexSelected,1);
        }
      }).catch(error=>{
        console.log(error);
      }).then(()=>{

      });
    },


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
      this.getAllEmailsAPI();
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
