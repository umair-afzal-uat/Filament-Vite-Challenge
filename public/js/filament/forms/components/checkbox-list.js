function s({livewireId:i}){return{areAllCheckboxesChecked:!1,checkboxListOptions:[],search:"",visibleCheckboxListOptions:[],init:function(){this.checkboxListOptions=Array.from(this.$root.querySelectorAll(".fi-fo-checkbox-list-option-label")),this.updateVisibleCheckboxListOptions(),this.$nextTick(()=>{this.checkIfAllCheckboxesAreChecked()}),Livewire.hook("commit",({component:e,commit:t,succeed:o,fail:c,respond:h})=>{o(({snapshot:l,effect:r})=>{this.$nextTick(()=>{e.id===i&&(this.checkboxListOptions=Array.from(this.$root.querySelectorAll(".fi-fo-checkbox-list-option-label")),this.updateVisibleCheckboxListOptions(),this.checkIfAllCheckboxesAreChecked())})})}),this.$watch("search",()=>{this.updateVisibleCheckboxListOptions(),this.checkIfAllCheckboxesAreChecked()})},checkIfAllCheckboxesAreChecked:function(){this.areAllCheckboxesChecked=this.visibleCheckboxListOptions.length===this.visibleCheckboxListOptions.filter(e=>e.querySelector("input[type=checkbox]:checked")).length},toggleAllCheckboxes:function(){this.visibleCheckboxListOptions.forEach(e=>{let t=e.querySelector("input[type=checkbox]");t.disabled||(t.checked=!this.areAllCheckboxesChecked,t.dispatchEvent(new Event("change")))}),this.areAllCheckboxesChecked=!this.areAllCheckboxesChecked},updateVisibleCheckboxListOptions:function(){this.visibleCheckboxListOptions=this.checkboxListOptions.filter(e=>e.querySelector(".fi-fo-checkbox-list-option-label")?.innerText.toLowerCase().includes(this.search.toLowerCase())?!0:e.querySelector(".fi-fo-checkbox-list-option-description")?.innerText.toLowerCase().includes(this.search.toLowerCase()))}}}export{s as default};
