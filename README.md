# SpaceReservation

## 簡介
讓教職員及學生借用，系辦來管理的**自習室預約系統**


主要功能有下面幾項：

1. 登入系統（登入後才能借用，否則只能看到登入跟註冊頁面）
    * 註冊帳號（要有帳號名稱、密碼、email）
    * 登入（用帳號、密碼）
    * 登出
    
2. 借用人的部分
    * 查詢座位資訊（教室及座位、插座有無、是否已被借出、是否開放借用）
    * 查詢/取消自己的預約
    * 預約座位</br>
    選一個日期跟時段後選座位，且符合下面條件：
        * 只能預約 30 天內的座位
        * 每人每時段，只能借用一個座位
        * 每座位每時段，只能借給一個人
        * 該時段不在「不開放借用」的範圍裡
    * 預約或取消成功後，會寄 Email 通知借用人

3. 系辦或管理員的部分
    * 新增自習室及其座位
    * 查詢及修改座位資訊
    * 查詢所有人的預約紀錄
    * 設定「不開放借用」的日期


## 使用說明與介面
### 登入系統
#### 登入
<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/f2ec6a1d-990a-4f2f-b271-d2d9847384ff" />

* 如果帳號不存在或密碼錯誤會跳出提示視窗</br>
    <img width="350"  alt="image" src="https://github.com/user-attachments/assets/36c2b870-a9a2-45fd-b2e4-b67ebb7ed796" />  <img width="350" alt="image" src="https://github.com/user-attachments/assets/beb13f10-2332-4900-9c40-c599d3ccec72" />

* 按眼睛能顯示密碼</br>
    <img width="400" height="233" alt="image" src="https://github.com/user-attachments/assets/3da42788-470a-4a8c-8033-eaf7086aec5e" />

&nbsp;&nbsp; - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  - - -
#### 註冊帳號
註冊成功後會跳到登入頁面 (勾選 `管理員` 登入後才會出現管理員的功能選單)
<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/a35dfa8f-0d33-4408-ba27-c5787045521b" />

* 如果空白會出現提示，email格式不對、兩個密碼不一樣、註冊帳號已存在也會</br>
    <img height="110" alt="image" src="https://github.com/user-attachments/assets/e475e79e-b2fc-4e87-92ac-3ad5a6661faf" />
  <img height="110"  alt="image" src="https://github.com/user-attachments/assets/59da2a97-03a5-4087-9b75-673d9bb73a58" />
  <img height="110" alt="image" src="https://github.com/user-attachments/assets/050b2ed2-53d8-4adf-8081-fc856d40be86" />

&nbsp;&nbsp; - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  - - -
#### 登出
按 `否` 會跳到首頁，按 `是` 會登出並跳到登入畫面
<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/06ec8af4-9128-4ed1-ab52-457a73888227" />

### 借用人
#### 首頁
<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/c064854e-fb9d-41eb-ac7d-4ca048d8b718" />

&nbsp;&nbsp; - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  - - -
#### 查詢座位
紅色是已被預約、灰色是不開放、藍色是空座位、插頭符號代表有插座
<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/e299dcb7-47ae-430e-a800-288c7814337b" />

&nbsp;&nbsp; - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  - - -
#### 預約 (只有藍色座位可選)</br>
<img width="1920" height="1080" alt="螢幕擷取畫面 2025-08-05 123729" src="https://github.com/user-attachments/assets/477d4540-38ed-4797-908a-4cbce53b01b3" />

* 只能選30天內日期，一次一小時，</br>
  <img width="300" alt="螢幕擷取畫面 2025-08-05 123608" src="https://github.com/user-attachments/assets/dc1c7901-9ff9-4f9f-9d87-d2008dd9e4db" />
  <img width="155" height="480" alt="螢幕擷取畫面 2025-08-05 123715" src="https://github.com/user-attachments/assets/9b4fa816-be4e-439e-9165-ba203cf137c3" />

* 點選座位後確認，預約成功座位會變紅色，收到mail</br>
  <p>
    <img height="130" align="top" alt="圖1" src="https://github.com/user-attachments/assets/bf8730c3-700a-464e-876b-db319a2cbef5" />
    <img height="130" align="top" alt="圖2" src="https://github.com/user-attachments/assets/74dcfae5-f22f-4b10-8c06-e4d3132e8b9c" />
    <img height="350" align="top" alt="圖3" src="https://github.com/user-attachments/assets/7974f1e2-6200-4f86-adcc-e8fb637a8ab9" />
  </p>

* 如果同日期同時段已有預約就不能再預約 (黃色座位是使用者這次選的)</br>
  <img height="400" alt="螢幕擷取畫面 2025-08-05 124048" src="https://github.com/user-attachments/assets/5a7b4b99-8d7a-48f6-9a3b-642b6d7362aa" />

&nbsp;&nbsp; - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  - - -
#### 查詢/取消預約紀錄
可依日期、時段、教室來查，沒輸入的項目就不限制，全部印出來</br>
紀錄會由新到舊排
<img width="1920" height="1080" alt="螢幕擷取畫面 2025-08-05 132708" src="https://github.com/user-attachments/assets/d560d57a-931b-4d8a-999c-79e4335d295c" />

* 按紅色按鈕能取消預約，成功會收到mail</br>
  <img height="300" alt="螢幕擷取畫面 2025-08-05 133712" src="https://github.com/user-attachments/assets/18043f61-c252-41d3-9aca-f943957b06f8" /> <img height="300" alt="Screenshot_20250805-153421" src="https://github.com/user-attachments/assets/0b1a38ce-3e52-4a2b-811f-4a73ec94ef02" />

### 管理者
* 首頁比借用人的多了 `取消座位（無此座位）` 的圖示、帳號編號
#### 新增自習室座位
* 輸入完，按 `產生座位` 就會出現</br>
   <img height="200" alt="螢幕擷取畫面 2025-08-05 144055" src="https://github.com/user-attachments/assets/cbd6db1c-b02e-4848-8a3b-8db6f37fc64e" />
    <img height="200" alt="螢幕擷取畫面 2025-08-05 143521" src="https://github.com/user-attachments/assets/10b3eb74-7039-4279-a4f8-5c7ead129e0d" />

座位點一次會設為有插座 (有插頭圖案的)，再點一次會取消那個座位 (白色有虛線的)，再點一次會復原回普通座位</br>
(滑鼠移到座位上時顏色會變深)
<img width="1920" height="1080" alt="螢幕擷取畫面 2025-08-05 143611" src="https://github.com/user-attachments/assets/2f361a1c-eccf-4bdf-8412-0e44f913aadc" />

&nbsp;&nbsp; - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  - - -
#### 查詢/修改座位
只能取消或新增插座
<img width="1920" height="1080" alt="螢幕擷取畫面 2025-08-05 150126" src="https://github.com/user-attachments/assets/3b87be77-7f1a-49e3-baf4-eae42a650558" />

&nbsp;&nbsp; - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  - - -
#### 查詢預約紀錄
比借用人的多了搜尋 `使用者` 的欄位，不能取消預約
<img width="1920" height="1080" alt="螢幕擷取畫面 2025-08-05 150110" src="https://github.com/user-attachments/assets/a42a2219-de75-4551-9f72-f51264f927bd" />

&nbsp;&nbsp; - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  - - -
#### 不開放日期
可一次選擇多個座位 (深藍色是被選到的)，設定完後按儲存，成功會有提示視窗
<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/aef7723b-f1b1-469a-b32a-44ee220080ef" />

### 其他
#### 下拉式選單
把頁面縮小就會出現</br>
<img height="350" alt="螢幕擷取畫面 2025-08-05 141935" src="https://github.com/user-attachments/assets/d045b6d8-ae95-45c9-a7bd-3c51bfb7ea49" /> 
<img height="350" alt="螢幕擷取畫面 2025-08-05 141958" src="https://github.com/user-attachments/assets/7971711a-fd9c-4f3c-9c06-620131a23003" />

## 如何安裝
1. 下載XAMPP（[連結](https://www.apachefriends.org)），要勾選Apache、MySQL、PHP、phpMyAdmin
2. 打開Apache跟MySQL</br>
     <img height="400" alt="螢幕擷取畫面 2025-08-05 104958" src="https://github.com/user-attachments/assets/8903da6e-308c-4052-82a9-5babb859725f" />

3. 移動到xampp中的htdocs，下載專案到電腦本地端
    ```
    cd C:\xampp\htdocs
    git clone https://github.com/Dora-meow/SpaceReservation.git SpaceReservation
    ```
    * 也可點進[https://github.com/Dora-meow/SpaceReservation](https://github.com/Dora-meow/SpaceReservation)
    按綠色的code按鈕下載壓縮檔，解壓縮後連同資料夾放入htdocs
4. 進 [http://localhost/phpmyadmin](http://localhost/phpmyadmin) 新增名字為 `labdemo` 的資料庫，點進去按 `匯入` 把 `SpaceReservation` 中的 `labdemos.sql` 匯入
4. 用瀏覽器打開網站 [http://localhost/labdemo/login.php](http://localhost/labdemo/login.php)</br>
(在還沒登入前不管先點進哪個頁面都會跳到這裡)
