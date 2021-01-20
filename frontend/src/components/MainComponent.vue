<template>
  <div class="page-container md-layout-row">
    <md-app md-mode="fixed">
      <!-- barra superior -->
      <md-app-toolbar class="md-primary" >
        <md-button class="md-icon-button" @click="menuVisible = !menuVisible">
          <md-icon>menu</md-icon>
        </md-button>
        <h3 class="md-title">{{ this.title }}</h3>
        <!--Opciones de seleccion-->
        <div class="md-toolbar-section-end" v-if="optionMenuSelected !== 3">
          <md-button v-if="!selected" class="md-icon-button" @click="reload">
            <md-icon>refresh</md-icon>
          </md-button>
          <div v-else>
            <md-button @click="markAsImportantAPI(null, true)"
                       v-if="optionMenuSelected === 0 || optionMenuSelected === 1" class="md-icon-button" >
              <md-icon>grade</md-icon>
              <md-tooltip md-delay="300">Mark as important</md-tooltip>
            </md-button>
            <md-button @click="markAsReadAPI(null)" class="md-icon-button">
              <md-icon>drafts</md-icon>
              <md-tooltip >Mark as read</md-tooltip>
            </md-button>
            <md-button @click="sentToTrashAPI" v-if="optionMenuSelected === 0 || optionMenuSelected === 1" class="md-icon-button" >
              <md-icon>delete</md-icon>
              <md-tooltip >To trash</md-tooltip>
            </md-button>
            <md-button @click="restoreEmailsAPI" v-if="optionMenuSelected === 2" class="md-icon-button" >
              <md-icon>restore_from_trash</md-icon>
              <md-tooltip >Restore</md-tooltip>
            </md-button>
            <md-button class="md-dense md-primary" @click="deleteMailsSelectedAPI" v-if="optionMenuSelected === 2" >
              Delete forever
              <md-tooltip >Delete</md-tooltip>
            </md-button>
          </div>
        </div>
        <!--Fin de las opciones-->
      </md-app-toolbar>
      <!-- fin de la barra superior -->

      <!-- inicio de la barra lateral -->
      <md-app-drawer md-permanent="full" :md-active.sync="menuVisible">
        <md-toolbar class="md-transparent" md-elevation="0">
          <span class="md-title">Menu</span>
        </md-toolbar>
        <md-list>
          <md-list-item>
            <md-button @click="showDialogNewEmail" class="button-primary md-dense md-raised md-primary"> <md-icon>add</md-icon> New email</md-button>
          </md-list-item>
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
        <NewEmailComponent
            ref="newEmailC"
             @addNewEmail="addNewEmail"
        ></NewEmailComponent>

        <EmailsComponent
            :loaded="loaded" :label="label" :icon="icon" :mails="filtered" :selected="selected"
            v-show="optionMenuSelected !== 3"
            @view="viewEmail" @markAsRead="markAsReadAPI" @markImportant="markAsImportantAPI"
            ref="inboxC"
        ></EmailsComponent>

        <ViewEmailComponent
            v-show="optionMenuSelected === 3"
            ref="viewC"
        ></ViewEmailComponent>
      </md-app-content>

    </md-app>

  </div>
</template>

<script>
import EmailsComponent from "@/components/EmailsComponent.vue";
import NewEmailComponent from "@/components/NewEmailComponent";
import ViewEmailComponent from "@/components/ViewEmailComponent";
import moment from "moment";

/**
 * Funcion que permite comparar las fechas que fueron creados dos correos
 * @param emailX {string}
 * @param emailY {string}
 * @returns {boolean} el resultado de la comparacion
 */
function orderByCreatedAt(emailX, emailY){
  return moment(emailX.created_at).isAfter(emailY.created_at);
}

export default {
  name: 'MainComponent',
  components:{ViewEmailComponent, EmailsComponent,  NewEmailComponent},
  data: () => ({
    menuVisible: true,
    optionMenuSelected:0,
    title: 'Inbox',
    icon:'inbox',
    label:'Inbox',
    mails:[],
    loaded:false,
    showDialog:false,
  }),
  computed:{
    /**
     * Esta variable computada es usada para filtrar los correos que se van a visualizar,
     * de acuerdo a la opcion seleccionado
     */
    filtered(){
      if (this.optionMenuSelected === 0){
        return this.mails.filter(x=>x.mail_to === null && !x.is_deleted).sort(orderByCreatedAt);
      }else if(this.optionMenuSelected === 1){
        return this.mails.filter(x=>x.mail_to !== null && !x.is_deleted).sort(orderByCreatedAt);
      } else if(this.optionMenuSelected === 2){
        return this.mails.filter(x => x.is_deleted).sort(orderByCreatedAt);
      }else{
        return this.mails;
      }
    },
    /**
     * Esta variable computada devuelve la cantidad de elementos que se encuentran seleccionados
    */
    selected() {
      return this.mails.filter(x=>x.is_selected).length;
    },
  },
  mounted() {
    this.getAllEmailsAPI();
  },
  methods:{
    /**
     * Desmarca los correos seleccionados
     */
    clearSelect(){
      this.mails.forEach(mail=>{
        mail.is_selected = false;
      });
    },

    /**
     * Es utilizado para obtener el texto que se visualizara en caso de que el emisor sea uno mismo (yo).
     */
    getMail(mail){
      if (mail.mail_from){
        if (mail.mail_to){
          return mail.mail_to;
        }
        return mail.mail_from;
      }else{
        if (mail.mail_to)
          return mail.mail_to;
        return 'Me';
      }
    },

    /**
     * Agrega propiedades extras a un correo
     *
     * is_selected - para identificar si el correo esta seleccionado.
     * first_letter - para mostrar la primera letra en la lista
     * created_from_now - muestra hace cuanto se creo el correo
     */
    addExtraProperties(mail){
      mail.is_selected = false;
      mail.first_letter = this.getMail(mail).substring(0,1).toUpperCase();
      mail.created_from_now = moment(mail.created_at).fromNow();
    },

    /**
     * Consumo de la API REST para obtener los correos almacenados
     */
    getAllEmailsAPI(){
      this.loaded = false;
      this.axios.get('http://'+location.hostname+':8000/mails').then((response)=>{
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
    /**
     * Consumo de la API REST para marcar como importante varios correos
     * @param id {int} id del email - se usa para cuando se realizar un solo cambio
     * @param newStatus - true para marcalo, false para desmarcarlo
     */
    markAsImportantAPI(id, newStatus){
      this.loaded = false;
      let data = {
        id_mails:[],
        is_important: true
      };
      if (id == null){
        this.mails.filter(mail=>mail.is_selected).forEach(mail=>{
          data.id_mails.push(mail.id);
        });
      }
      else{
        data.id_mails.push(id);
        data.is_important = newStatus;
      }

      if (!data.is_important)
        this.$refs.inboxC.onShowUnmarkedSuccessful(false);
      else
        this.$refs.inboxC.onShowMarkedSuccessful(false);

      this.axios.put('http://'+location.hostname+':8000/mails/mark',data).then(()=>{

        if (!data.is_important)
          this.$refs.inboxC.onShowUnmarkedSuccessful(true);
        else
          this.$refs.inboxC.onShowMarkedSuccessful(true);

        if (id!=null){
          let indexMail = this.mails.findIndex(m=> m.id === id);
          this.mails[indexMail].is_important = newStatus;
        }

        this.mails.forEach(mail=>{
          if (mail.is_selected)
            mail.is_important = true;
        });
        this.clearSelect();
      }).catch(error=>{
        console.log(error);
      }).then(()=>{
        this.loaded = true;
      });
    },
    /**
     * Consumo de la API REST para enviar a la basura un conjunto de correos seleccionados
     */
    sentToTrashAPI(){
      let data = {
        id_mails:[],
        is_deleted:true
      };
      this.loaded = false;
      this.mails.filter(mail=>mail.is_selected).forEach(mail=>{
        data.id_mails.push(mail.id);
      });
      this.axios.put('http://'+location.hostname+':8000/mails/trash',data).then(()=>{
        this.$refs.inboxC.onShowTrashSuccessful(true);
        this.mails.forEach(mail=>{
          if (mail.is_selected)
            mail.is_deleted = true;
        });
        this.clearSelect();
      }).catch(error=>{
        console.log(error);
      }).then(()=>{
        this.loaded = true;
      });
    },
    /**
     * Consumo de la API REST para marcar como leido los correos seleccionados
     * @param id {int} id del correo - solo se usa para mandar un unico correo
     */
    markAsReadAPI(id = null){
      this.loaded = false;
      let data = {
        id_mails:[]
      };
      if (id == null){
        this.mails.filter(mail=>mail.is_selected).forEach(mail=>{
          data.id_mails.push(mail.id);
        });
      }
      else{
        data.id_mails.push(id);
      }
      this.axios.put('http://'+location.hostname+':8000/mails/read',data).then(()=>{
        if (id != null){
          let indexMail = this.mails.findIndex(m=> m.id === id);
          this.mails[indexMail].is_read = !this.mails[indexMail].is_read;
        }else{
          this.$refs.inboxC.onShowMarkAsReadSuccessful(true);
        }

        this.mails.forEach(mail=>{
          if (mail.is_selected)
            mail.is_read = true;
        });
        this.clearSelect();
      }).catch(error=>{
        console.log(error);
      }).then(()=>{
        this.loaded = true;
      });
    },
    /**
     * Consumo de la API REST para borrar los correos seleccionados permanente
     */
    deleteMailsSelectedAPI(){
      let data = {
        id_mails:[]
      };
      this.mails.filter(mail=>mail.is_selected).forEach(mail=>{
        data.id_mails.push(mail.id);
      });
      this.axios.put('http://'+location.hostname+':8000/mails/delete',data).then(()=>{

        this.$refs.inboxC.onShowDeleteSuccessful(true);

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
    /**
     * Consumo de la API REST para restaurar correos a la bandeja principal
     */
    restoreEmailsAPI(){
      let data = {
        id_mails:[],
        is_deleted:false
      };
      this.mails.filter(mail=>mail.is_selected).forEach(mail=>{
        data.id_mails.push(mail.id);
      });
      this.axios.put('http://'+location.hostname+':8000/mails/trash',data).then(()=>{
        this.$refs.inboxC.onShowRestoreSuccessful(true);
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


    /**
     * Permite realizar cambios entre las opciones (inbox, sent, trash)
     *
     * @param option {int} cada opcion tiene identificado un numero (inbox -0 , sent - 1, trash -2)
     */
    changeOption(option){
      this.clearSelect();
      this.menuVisible = false;
      this.optionMenuSelected = option;
      if (option === 0){
        this.title = this.label = 'Inbox';
        this.icon = 'inbox';
      }else if(option === 1){
        this.title = this.label = 'Sent';
        this.icon = 'send';
      }else if(option === 2){
        this.title = this.label = 'Trash';
        this.icon = 'delete_outline';
      }
    },

    /**
     * Actualiza la lista de correos
     */
    reload(){
      this.getAllEmailsAPI();
    },

    /**
     * Muestra el modal para agregar un correo nuevo
     */
    showDialogNewEmail(){
      this.$refs.newEmailC.showDialog();
    },
    /**
     * Agrega el correo guardado en la base de datos a la lista
     * @param email
     */
    addNewEmail(email){
      this.addExtraProperties(email);
      this.mails.push(email);
    },
    /**
     * Permite visualizar un correo especifico
     * @param email {json} - datos del email
     */
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
  .button-primary{
    padding: 10px !important;
    width: 100%;
  }
</style>
