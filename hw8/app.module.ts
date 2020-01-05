import { BrowserModule } from '@angular/platform-browser';
import { NgModule, ViewChild } from '@angular/core';
import { HttpClientModule } from '@angular/common/http';
import { ChartsModule } from 'ng2-charts';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { WeatherFormComponent } from './components/weather-form/weather-form.component';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { TabsComponent } from './components/tabs/tabs.component';
import { CurrentTabComponent } from './components/current-tab/current-tab.component';
import { WeeklyTabComponent } from './components/weekly-tab/weekly-tab.component';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { StorageServiceModule } from 'ngx-webstorage-service';
import { MatInputModule, MatSelectModule, MatAutocomplete, MatAutocompleteModule, MatFormFieldModule } from '@angular/material'
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';


const material = [
  MatInputModule,
  MatSelectModule,
  MatAutocomplete,
  MatFormFieldModule
]

@NgModule({
  declarations: [
    AppComponent,
    WeatherFormComponent,
    TabsComponent,
    CurrentTabComponent,
    WeeklyTabComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    FormsModule,
    ReactiveFormsModule,
    HttpClientModule,
    ChartsModule,
    NgbModule,
    StorageServiceModule,
    MatInputModule,
    MatSelectModule,
    MatAutocompleteModule,
    BrowserAnimationsModule
  ], 
  entryComponents: [TabsComponent, CurrentTabComponent],
  providers: [WeatherFormComponent, TabsComponent],
  bootstrap: [AppComponent]
})
export class AppModule { }
