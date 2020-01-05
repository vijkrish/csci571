import { Component } from '@angular/core';
import { TabsComponent } from './components/tabs/tabs.component';
import { CurrentTabComponent } from './components/current-tab/current-tab.component';
 

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})


export class AppComponent {
  title = 'weather-app';

  dummyComponent = TabsComponent;

  assignComponent(component) {
    this.dummyComponent = component;
  }

}
