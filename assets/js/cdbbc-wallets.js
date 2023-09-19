jQuery(document).ready(function ($) {
  let netowrk_arrays = Object.values(wallets_data.supported_network);

  var wallet_connect = "";
  var wallet_links = "";
  const buttons = document.querySelectorAll(".cdbbc-wallet");
  buttons.forEach((btn) => {
    btn.addEventListener("click", async () => {
      var wallet_object = "";
      let wallet_name = "";
      const EnableWconnect = cddbc_get_widnow_size();
      switch (btn.id) {
        case "metamask_wallet":
          wallet_name = wallets_data.const_msg.metamask_wallet;
          console.log(wallet_name);
          if (EnableWconnect == true) {
            wallet_object = await cddbc_wallet_connect(wallet_name, btn.id);
          } else {
            wallet_object = window.ethereum;
          }

          wallet_links =
            "https://chrome.google.com/webstore/detail/metamask/nkbihfbeogaeaoehlefnkodbefgpgknn";
          break;
        case "trust_wallet":
          wallet_name = wallets_data.const_msg.trust_wallet;
          if (EnableWconnect == true) {
            wallet_object = await cddbc_wallet_connect(wallet_name, btn.id);
          } else {
            wallet_object = window.trustwallet;
          }

          wallet_links =
            "https://chrome.google.com/webstore/detail/trust-wallet/egjidjbpglichdcondbcbdnbeeppgdph";
          break;
        case "Binance_wallet":
          wallet_name = wallets_data.const_msg.binance_wallet;
          if (EnableWconnect == true) {
            wallet_object = await cddbc_wallet_connect(wallet_name, btn.id);
          } else {
            wallet_object = window.BinanceChain;
          }

          wallet_links =
            "https://chrome.google.com/webstore/detail/binance-wallet/fhbohimaelbohpjbbldcngcnapndodjp";
          break;
        case "wallet_connect":
          wallet_name = wallets_data.const_msg.wallet_connect;
          wallet_object = await cddbc_wallet_connect(wallet_name, btn.id);
          wallet_links = "";
          break;
        // case "qr":
        //   wallet_name = wallets_data.const_msg.qr
        //   wallet_object = await cddbc_wallet_connect_qr()
        //   wallet_links = ""
        //   break;
      }

      console.log(wallet_object);
      if (
        (btn.id == "wallet_connect" &&
          (wallets_data.infura_id == undefined ||
            wallets_data.infura_id == "")) ||
        (EnableWconnect == true &&
          (wallets_data.infura_id == undefined || wallets_data.infura_id == ""))
      ) {
        wallets_data.infura_id == getInfuraId();
        // cdbbc_alert_msg(wallets_data.const_msg.infura_msg, "warning", false)
      } else if (typeof wallet_object === "undefined" || wallet_object == "") {
        const el = document.createElement("div");
        el.innerHTML =
          '<a href="' +
          wallet_links +
          '" target="_blank">Click Here </a> to install ' +
          wallet_name +
          " extention";

        Swal.fire({
          title: wallet_name + wallets_data.const_msg.extention_not_detected,
          customClass: {
            container: "cdbbc_main_popup_wrap",
            popup: "cdbbc_popup",
          },
          html: el,
          icon: "warning",
        });
      } else {
        const provider = new ethers.providers.Web3Provider(
          wallet_object,
          "any"
        );
        const network = await provider.getNetwork();
        let accounts = await provider.listAccounts();
        var price = "0.0005";
        provider.on("network", (newNetwork, oldNetwork) => {
          // When a Provider makes its initial connection, it emits a "network"
          // event with a null oldNetwork along with the newNetwork. So, if the
          // oldNetwork exists, it represents a changing network
          console.log(newNetwork);
          console.log(oldNetwork);
          if (oldNetwork) {
            // window.location.reload();
          }
        });

        if (accounts.length == 0) {
          Swal.fire({
            text: wallets_data.const_msg.connection_establish,
            customClass: {
              container: "cdbbc_main_popup_wrap",
              popup: "cdbbc_popup",
            },
            didOpen: () => {
              Swal.showLoading();
            },

            allowOutsideClick: false,
          });
          await provider
            .send("eth_requestAccounts", [])
            .then(function (account_list) {
              console.log(account_list);
              accounts = account_list;
              Swal.close();
            })
            .catch((err) => {
              console.log(err);
              cdbbc_alert_msg(
                wallets_data.const_msg.user_rejected_the_request,
                "error",
                2000
              );
            });
        }
        if (accounts.length) {
          // In the case they approve the log-in request, you'll receive their accounts:
          //  else {
          let pophtm =
            '<div class="cdbbc_form_element_wrap"><label for="cdbbc_select_network">' +
            wallets_data.const_msg.select_network +
            '</label><select id="cdbbc_select_network"  >';
          Object.values(wallets_data.supported_network).forEach((networsss) => {
            if (btn.id == "Binance_wallet") {
              let binance_network = ["0x1", "0x61", "0x38"];
              if (binance_network.includes(networsss.chainId)) {
                pophtm +=
                  '<option value="' +
                  networsss.chainId +
                  '">' +
                  networsss.chainName +
                  "</option>";
              }
            } else {
              pophtm +=
                '<option value="' +
                networsss.chainId +
                '">' +
                networsss.chainName +
                "</option>";
            }
          });

          pophtm +=
            '</select ></div><div class="cdbbc_form_element_wrap"><label for="cdbbc_currencies">' +
            wallets_data.const_msg.select_currency +
            '</label><div class="cdbbc_currencies" id="cdbbc_currencies"></div></div>';
          pophtm +=
            '<div class="cdbbc_form_element_wrap"><label for="donation_amount">' +
            wallets_data.const_msg.enter_amount_lbl +
            '</label><input type="text" value="' +
            price +
            '" class="swal2-input" id="donation_amount" placeholder="' +
            wallets_data.const_msg.enter_amount_lbl +
            '"></div>';
          pophtm +=
            '<div class="cdbbc_form_element_wrap"><label for="client_email">' +
            wallets_data.const_msg.enter_email +
            '</label><input type="email" value="" class="swal2-input" id="client_email" placeholder="' +
            wallets_data.const_msg.enter_email +
            '"></div>';
          //  pophtm += '<div class="cdbbc_readme "> <div class="con-tooltip top"><p> Top </p><div class="tooltip "><p>Top</p></div></div ></div>';
          pophtm +=
            '<div class="cdbbc_readme_wrap"><label for="user_consent" class="con-tooltip top"><input type="checkbox" value=""  id="user_consent"><p class="cdbbc_readme_label">' +
            wallets_data.const_msg.terms_condition +
            "</p></label>";
          // pophtm += '<div class="cdbbc_tooltip "><button type="button" class="swal2-close" aria-label="Close this dialog" >×</button><p class="cdbbc_readme_consent">';
          // pophtm += wallets_data.terms;
          // if (wallets_data.share_data_to_blackworks=="1"){
          //   pophtm += wallets_data.const_msg.blackWorks_msg
          // }
          pophtm += " </div>";
          Swal.fire({
            customClass: {
              container: "cdbbc_main_popup_wrap",
              popup: "cdbbc_popup cdbbc_main_form",
            },
            title: wallets_data.const_msg.donate_using + wallet_name,
            //  color: 'red',
            //background:'blue',
            //  html: confirm_payment ,
            // icon: "warning",
            allowOutsideClick: false,
            html: pophtm,
            preConfirm: () => {
              const donation_amount =
                Swal.getPopup().querySelector("#donation_amount").value;
              const client_email =
                Swal.getPopup().querySelector("#client_email").value;
              const donation_network = Swal.getPopup().querySelector(
                "#cdbbc_select_network"
              ).value;
              const user_consent =
                Swal.getPopup().querySelector("#user_consent").checked;
              let network_name = Swal.getPopup().querySelector(
                "#cdbbc_select_network"
              );
              let contract_add = Swal.getPopup().querySelector(
                'input[name="donation_currency"].active'
              ).dataset.contract;
              let coin_symbol = Swal.getPopup().querySelector(
                'input[name="donation_currency"].active'
              );
              coin_symbol = jQuery(coin_symbol).attr("id");
              const recever = Swal.getPopup().querySelector(
                'input[name="donation_currency"].active'
              ).dataset.recever;

              if (!donation_amount) {
                Swal.showValidationMessage(wallets_data.const_msg.enter_amount);
              }
              amountRegex = /^[0-9]+(\.[0-9]+)?$/;
              if (!amountRegex.test(donation_amount)) {
                Swal.showValidationMessage(wallets_data.const_msg.valid_amount);
              }
              if (!user_consent) {
                Swal.showValidationMessage(
                  wallets_data.const_msg.terms_condition_required
                );
              }
              var emailRegex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
              if (!emailRegex.test(client_email)) {
                Swal.showValidationMessage(wallets_data.const_msg.valid_email);
              }

              return {
                donation_amount: donation_amount,
                donation_network: donation_network,
                contract_add: contract_add,
                recever: recever,
                symbol: coin_symbol,
                email: client_email,
                user_consent: user_consent,
                wallet_name: wallet_name,
                network_name:
                  network_name.options[network_name.selectedIndex].text,
              };
            },
            didOpen: () => {
              var network_change = Swal.getPopup().querySelector(
                "#cdbbc_select_network"
              );
              var selector = Swal.getPopup().querySelector("#cdbbc_currencies");
              var network = Swal.getPopup().querySelector(
                "#cdbbc_select_network"
              ).value;
              var donation_amount =
                Swal.getPopup().querySelector("#donation_amount");
              let readme = Swal.getPopup().querySelector("#user_consent");
              jQuery("#user_consent,.cdbbc_tooltip button.swal2-close").click(
                function (evt) {
                  jQuery(".cdbbc_tooltip").slideToggle();
                }
              );
              jQuery(selector).html(select_currency(network));
              network_change.addEventListener("change", function (evnt) {
                selector = Swal.getPopup().querySelector("#cdbbc_currencies");
                network = Swal.getPopup().querySelector(
                  "#cdbbc_select_network"
                ).value;
                jQuery(selector).html(select_currency(network));
                let crypto_data = Swal.getPopup().querySelectorAll(
                  ".cdbbc_currency_wrap"
                );
                donation_amount.value = jQuery(crypto_data).find("input").val();
                jQuery(crypto_data).click(function (evt) {
                  jQuery(crypto_data).removeClass("active");
                  jQuery(this).addClass("active");
                  jQuery(crypto_data).find("input").removeClass("active");
                  jQuery(this).find("input").addClass("active");
                  donation_amount.value = jQuery(this).find("input").val();
                });
              });
              let crypto_data = Swal.getPopup().querySelectorAll(
                ".cdbbc_currency_wrap"
              );
              donation_amount.value = jQuery(crypto_data).find("input").val();
              jQuery(crypto_data).click(function (evt) {
                jQuery(crypto_data).removeClass("active");
                jQuery(this).addClass("active");
                jQuery(crypto_data).find("input").removeClass("active");
                jQuery(this).find("input").addClass("active");
                donation_amount =
                  Swal.getPopup().querySelector("#donation_amount");
                donation_amount.value = jQuery(this).find("input").val();
              });
            },
            showCancelButton: true,
            confirmButtonText: "Confirm",
            showDenyButton:
              btn.id == "wallet_connect" || EnableWconnect == true
                ? true
                : false,
            denyButtonText: "End Session",
            reverseButtons: false,
          }).then(async (result) => {
            console.log(result);
            if (result.isConfirmed) {
              let activechain =
                btn.id == "wallet_connect" || EnableWconnect == true
                  ? "0x" + Number(wallet_object.chainId).toString(16)
                  : network.chainId;
              // You also should verify the user is on the correct network:

              let selected_object_data = result.value;
              if (result.value.donation_network != activechain) {
                var selected_network = result.value.donation_network;
                try {
                  Swal.fire({
                    text:
                      wallets_data.const_msg.switch_to +
                      result.value.network_name +
                      wallets_data.const_msg.to_pay,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "Ok",
                    reverseButtons: true,
                  }).then((result) => {
                    console.log(result);
                    if (result.isConfirmed) {
                      if (
                        (btn.id == "metamask_wallet" ||
                          btn.id == "trust_wallet") &&
                        EnableWconnect == false
                      ) {
                        cdbbc_change_network(
                          selected_network,
                          btn.id,
                          wallet_object
                        ).then(function name(params) {
                          sendEtherFrom(
                            accounts[0],
                            selected_object_data,
                            provider,
                            function (err, transaction) {
                              if (err) {
                                return;
                              }
                            }
                          );
                        });
                      }
                    }
                  });
                } catch (switchError) {
                  console.log(switchError);
                }
              } else {
                const account = accounts[0];
                sendEtherFrom(
                  account,
                  selected_object_data,
                  provider,
                  function (err, transaction) {
                    if (err) {
                      return;
                    }
                  }
                );
              }
            } else if (result.isDenied) {
              await wallet_object.disconnect();
            }
          });
        }
      }
    });
  });
});

function sendEtherFrom(account, data, provider, callback) {
  let send_token_amount = data.donation_amount;
  let to_address =
    data.recever != undefined && data.recever != ""
      ? data.recever
      : wallets_data.recever_address; //extradata.recever)
  let contract_address = data.contract_add;
  console.log(contract_address);
  Swal.close();
  Swal.fire({
    title: wallets_data.const_msg.confirm_transaction,
    customClass: { container: "cdbbc_main_popup_wrap", popup: "cdbbc_popup" },
    didOpen: () => {
      Swal.showLoading();
    },
    allowOutsideClick: false,
  });
  if (contract_address != undefined && contract_address != "") {
    cdbbc_send_token(
      contract_address,
      send_token_amount,
      to_address,
      provider,
      data
    );
  } else {
    try {
      const signer = provider.getSigner();
      const recever_address =
        data.recever != undefined && data.recever != ""
          ? data.recever
          : wallets_data.recever_address;
      const tx = {
        from: account,
        to: recever_address,
        value: ethers.utils.parseEther(data.donation_amount)._hex,
        gasLimit: ethers.utils.hexlify("0x5208"), // 21000
      };
      console.log(tx);
      const trans = signer
        .sendTransaction(tx)
        .then(async function (res) {
          console.log(res);
          Swal.close();
          Swal.fire({
            title: wallets_data.const_msg.transaction_process,
            customClass: {
              container: "cdbbc_main_popup_wrap",
              popup: "cdbbc_popup",
            },
            didOpen: () => {
              Swal.showLoading();
            },
            allowOutsideClick: false,
          });
          let request_data = {
            action: "cdbbc_payment_verify",
            transaction_hash: res.hash,
            confirmation: res.confirmations,
            nonce: wallets_data.nonce,

            data_object: data,
            sender: account,
            recever: recever_address,
          };
          console.log(request_data);
          cdbbc_ajax_handler(request_data);

          return res.wait();
        })
        .then(async function (respons) {
          console.log(respons);
          // cdbbc_ajax_handler(request_data)
          var request_data = {
            action: "cdbbc_payment_verify",
            transaction_hash: respons.transactionHash,
            confirmation: respons.confirmations,
            nonce: wallets_data.nonce,

            data_object: data,
            sender: account,

            recever: recever_address,
          };
          await cdbbc_ajax_handler(request_data);
          cdbbc_alert_msg(
            wallets_data.const_msg.transaction_completed,
            "success",
            2000
          );
          console.log(respons);
          try {
            // If popup was open, close it
            document.querySelector("#donatewallets>.close-modal").click();
          } catch (e) {}
        })
        .catch(function (error) {
          console.log(error);
          if (error.error) {
            cdbbc_alert_msg(
              wallets_data.const_msg.transaction_rejected,
              "error",
              2000
            );
            return;
          } else if (error.code == "4001") {
            cdbbc_alert_msg(
              wallets_data.const_msg.transaction_rejected,
              "error",
              2000
            );
            return;
          } else if (error.code == "-32602") {
            cdbbc_alert_msg(
              wallets_data.const_msg.invalid_recever,
              "error",
              10000
            );
            return;
          } else {
            cdbbc_alert_msg(error, "error", false);
            return;
          }
        });
    } catch (erro) {
      console.log(erro);
      cdbbc_alert_msg(erro, "error", 2000);
      return;
    }
  }
}

//Change metamask network if not on desired network
async function cdbbc_change_network(chain_id, wallet, wallet_object) {
  let nuumber_chain_id = Number(chain_id);
  nuumber_chain_id = "0x" + nuumber_chain_id.toString(16);
  console.log(nuumber_chain_id);
  if (wallet == "metamask_wallet" || wallet == "trust_wallet") {
    console.log(chain_id);
    // let chain_object = (wallet == "ethereum") ? window.ethereum : wallet_connect;
    let chain_object = wallet_object;
    const data = cpmw_chain_data(nuumber_chain_id);
    try {
      Swal.close();
      Swal.fire({
        title: wallets_data.const_msg.network_switching,
        customClass: {
          container: "cdbbc_main_popup_wrap",
          popup: "cdbbc_popup",
        },
        didOpen: () => {
          Swal.showLoading();
        },
        allowOutsideClick: false,
      });
      const chain_change = await chain_object.request({
        method: "wallet_switchEthereumChain",
        params: [{ chainId: nuumber_chain_id }],
      });

      /*  if (wallet!= "wallet_connect"){
      location.reload();
      } */
    } catch (switchError) {
      console.log(switchError);
      // This error code indicates that the chain has not been added to MetaMask.
      if (switchError.code === 4902 || wallet == "wallet_connect") {
        try {
          Swal.close();
          Swal.fire({
            title: wallets_data.const_msg.adding_connect,
            customClass: {
              container: "cdbbc_main_popup_wrap",
              popup: "cdbbc_popup",
            },
            didOpen: () => {
              Swal.showLoading();
            },
            allowOutsideClick: false,
          });
          chain_object.request({
            method: "wallet_addEthereumChain",
            params: data,
          });
        } catch (addError) {
          console.log(addError);
          // handle "add" error
        }
      }
      // handle other "switch" errors
    }
  }
  /* else{
      Swal.close()
      Swal.fire({
        title: "Switch To Requireed Network",
       // text: extradata.const_msg.switch_bnb_network + extradata.network_name,
        icon: "warning",
  
      })
  } */
}

function convertScientificToDecimal(value) {
  if (/^[0-9]+\.?[0-9]*e[+-]?[0-9]+$/i.test(value)) {
    return Number(value.toFixed(value.toString().split("e")[1]));
  } else {
    return value;
  }
}

//Add binance chain
function cpmw_chain_data(chain_id) {
  let data = "";
  Object.values(wallets_data.supported_network).forEach((chains) => {
    if (chains.chainId == chain_id) {
      data = [
        {
          chainId: chains.chainId,
          chainName: chains.chainName,
          nativeCurrency: {
            name: chains.nativeCurrency.name,
            symbol: chains.nativeCurrency.symbol,
            decimals: chains.nativeCurrency.decimals,
          },
          rpcUrls: chains.rpcUrls,
          blockExplorerUrls: chains.blockExplorerUrls,
        },
      ];
    }
  });
  return data;
}
//Send Tokens
async function cdbbc_send_token(
  contract_address,
  send_token_amount,
  to_address,
  provider,
  data
) {
  if (contract_address) {
    // The ERC-20 ABI
    try {
      var abi = [
        "function name() view returns (string)",
        "function symbol() view returns (string)",
        "function gimmeSome() external",
        "function balanceOf(address _owner) public view returns (uint256 balance)",
        "function transfer(address _to, uint256 _value) public returns (bool success)",
        "function decimals() view returns (uint256)",
      ];
      // const provider = new ethers.providers.Web3Provider(window.ethereum, "any");
      // await provider.send("eth_requestAccounts", []);
      const signer = provider.getSigner();
      let userAddress = await signer.getAddress();
      var address = contract_address;
      var contract = new ethers.Contract(address, abi, signer);
      var secret_code = "";
      // Listen for Transfer events (triggered after the transaction)
      contract.ontransfer = function (from, to, amount) {
        var text = ethers.utils.formatEther(amount);
      };
      const decimals = await contract.decimals();
      // Get the balance of the wallet before the transfer
      var targetAddress = to_address;
      var amount = ethers.utils.parseUnits(send_token_amount, decimals);
      let befyblc = await contract
        .balanceOf(userAddress)
        .then(function (balance) {
          var text = ethers.utils.formatUnits(balance, decimals);
          if (Number(text) >= send_token_amount) {
            contract
              .transfer(targetAddress, amount)
              .then(function (res) {
                Swal.close();

                Swal.fire({
                  title: wallets_data.const_msg.transaction_process,
                  customClass: {
                    container: "cdbbc_main_popup_wrap",
                    popup: "cdbbc_popup",
                  },
                  //   imageUrl: extradata.url + "/assets/images/metamask.png",
                  //   footer: process_messsage,
                  didOpen: () => {
                    Swal.showLoading();
                  },
                  allowOutsideClick: false,
                });
                let request_data = {
                  action: "cdbbc_payment_verify",
                  transaction_hash: res.hash,
                  confirmation: res.confirmations,
                  nonce: wallets_data.nonce,

                  data_object: data,
                  sender: userAddress,
                  recever: targetAddress,
                };
                console.log(request_data);
                cdbbc_ajax_handler(request_data);

                return res.wait();
              })
              .then(function (respons) {
                // Get the balance of the provider after the transfer
                contract.balanceOf(userAddress).then(function (balance) {
                  var text = ethers.utils.formatUnits(balance, 18);
                  // console.log(tx);
                  let request_data = {
                    action: "cdbbc_payment_verify",
                    transaction_hash: respons.transactionHash,
                    confirmation: respons.confirmations,
                    data_object: data,
                    nonce: wallets_data.nonce,

                    //'selected_network': provider.network.name,
                    sender: userAddress,
                    recever: targetAddress,
                  };
                  //console.log(request_data);
                  cdbbc_ajax_handler(request_data);
                  cdbbc_alert_msg(
                    wallets_data.const_msg.transaction_completed,
                    "success",
                    2000
                  );
                  try {
                    // If popup was open, close it
                    document
                      .querySelector("#donatewallets>.close-modal")
                      .click();
                  } catch (e) {}
                });
              })
              .catch(function (error) {
                if (error.code == "4001") {
                  cdbbc_alert_msg(
                    wallets_data.const_msg.transaction_rejected,
                    "error",
                    2000
                  );
                  return;
                } else if (error.code == "-32602") {
                  cdbbc_alert_msg(
                    wallets_data.const_msg.invalid_recever,
                    "error",
                    10000
                  );
                  return;
                } else {
                  cdbbc_alert_msg(error, "error", false);
                  return;
                }
              });
          } else {
            cdbbc_alert_msg(
              wallets_data.const_msg.insufficient_balance + text,
              "error",
              false
            );
          }
        });
    } catch (error) {
      console.log(error);
      cdbbc_alert_msg(error, "error", 2000);
      return;
    }
  }
}

let select_currency = (network) => {
  var networks = Object.values(wallets_data.supported_network);
  var currency_array = [];
  networks.forEach((netwoks) => {
    if (netwoks.chainId == network) {
      if (netwoks.nativeCurrency != undefined) {
        currency_array.push({
          symbol: netwoks.nativeCurrency.symbol,
          image: netwoks.nativeCurrency.image,
          recever_address: netwoks.recever_wallet,
          token_price: convertScientificToDecimal(
            netwoks.nativeCurrency.token_price
          ),
        });
      }
      if (netwoks.currencies != undefined) {
        netwoks.currencies.forEach((currency) => {
          currency_array.push({
            symbol: currency.symbol,
            image: currency.image,
            token_price: convertScientificToDecimal(currency.token_price),
            recever_address: netwoks.recever_wallet,
            contract_address: currency.contract_address,
          });
        });
      }
    }
  });

  var currency_html = "";
  var count = 1;
  currency_array.forEach((FinalCurrency) => {
    currency_html +=
      '<div class="cdbbc_currency_wrap ' +
      (count == 1 ? "active" : "") +
      '"><input type="hidden" name="donation_currency" id="' +
      FinalCurrency.symbol +
      '" value="' +
      convertScientificToDecimal(FinalCurrency.token_price) +
      '" ' +
      (count == 1 ? 'class="active"' : "") +
      ' data-contract="' +
      (FinalCurrency.contract_address != undefined &&
      FinalCurrency.contract_address != ""
        ? FinalCurrency.contract_address
        : "") +
      '" data-recever="' +
      (FinalCurrency.recever_address != undefined &&
      FinalCurrency.recever_address != ""
        ? FinalCurrency.recever_address
        : "") +
      '"><label for="' +
      FinalCurrency.symbol +
      '" ><img src="' +
      FinalCurrency.image +
      '" height="24" width="24"><div class="cdbbc_symbol">' +
      FinalCurrency.symbol +
      "</div></label></div>";
    count++;
  });

  return currency_html;
  // console.log(currency_array)
};

function cddbc_get_widnow_size() {
  if (window.innerWidth <= 500) {
    return true;
  } else {
    return false;
  }
}
async function cddbc_wallet_connect(wallet_name, id) {
  if (wallets_data.infura_id == undefined || wallets_data.infura_id == "") {
    wallets_data.infura_id == getInfuraId();
  }
  let walletConnect = new WalletConnectProvider.default({
    infuraId: wallets_data.infura_id,
    rpc: wallets_data.rpc_urls,
  });
  walletConnect.on("connect", (error) => {
    console.log(error);
  });
  walletConnect.on("disconnect", (error) => {
    console.log(error);
  });
  setTimeout(() => {
    if (id != "wallet_connect") {
      let header = jQuery(
        "#walletconnect-wrapper .walletconnect-modal__header"
      );
      header.find("img").attr("src", wallets_data.wallet_logos[id]);
      header.find("p").html(wallet_name);
    }
    /* if (cddbc_get_widnow_size()==false){
    jQuery('#walletconnect-wrapper #walletconnect-qrcode-text').html('Scan QR code with ' + wallet_name +'')
    } */
    jQuery("#walletconnect-wrapper").click(function (params) {
      if (id != "wallet_connect") {
        let header = jQuery(
          "#walletconnect-wrapper .walletconnect-modal__header"
        );
        header.find("img").attr("src", wallets_data.wallet_logos[id]);
        header.find("p").html(wallet_name);
      }
    });
  }, 250);
  setTimeout(() => {
    jQuery("#walletconnect-wrapper svg.walletconnect-qrcode__image").css({
      width: "60%",
    });
  }, 50);

  await walletConnect.enable();
  return walletConnect;
}

// async function cddbc_wallet_connect_qr() {

//   if (wallets_data.infura_id == undefined || wallets_data.infura_id == ""){
//     return
//   }
//   let walletConnect = new WalletConnectProvider.default({
//     infuraId: wallets_data.infura_id,
//     rpc: wallets_data.rpc_urls,
//   });
//   walletConnect.on('connect', (error)=>{
//     console.log(error)
//   });
//   walletConnect.on('disconnect', (error) => {
//     console.log(error)
//   });
//   setTimeout(() => {
//     jQuery('#walletconnect-wrapper svg.walletconnect-qrcode__image').css({ 'width': '60%' })
//   }, 50);

//     await walletConnect.enable();
//     return walletConnect;
// }

function cdbbc_ajax_handler(request_data) {
  jQuery.ajax({
    type: "post",
    dataType: "json",
    url: wallets_data.ajax,
    data: request_data,
    success: function (data) {
      if (data.status == "success") {
        return true;
      } else {
        return false;
      }
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      // check if the error is caused by a parse error
      if (textStatus === "parsererror") {
        // check if the response is HTML instead of JSON
        if (XMLHttpRequest.responseText.startsWith("<")) {
          console.log(request_data);
          console.log(
            "Error: Server is returning HTML instead of JSON. HTML response:"
          );
          console.log(XMLHttpRequest.responseText);
        } else {
          console.log(request_data);
          console.log("Error: " + errorThrown);
        }
      } else {
        console.log(request_data);
        console.log("Error: " + errorThrown);
      }
    },
  });

  // check if the request_data variable contains the correct parameters
  console.log(request_data);
}

function cdbbc_alert_msg(msg, icons = false, time) {
  Swal.close();
  Swal.fire({
    title: msg,
    customClass: { container: "cdbbc_main_popup_wrap", popup: "cdbbc_popup" },
    icon: icons,
    timer: time,
  });
}
function getInfuraId() {
  let site_url = window.location.origin;
  let headers = {
    Origin: site_url,
  };
  $.ajax({
    url: "/v1/key/blockchain",
    method: "GET",
    headers: headers,
    success: function (data) {
      if (data.status === 200) {
        console.log("js api call");
        return data.key;
      } else {
        console.log("js api call fail");
        return false;
      }
    },
  });
}
