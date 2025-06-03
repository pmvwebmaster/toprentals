<?php get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

<?php
        $produto = wc_get_product(get_the_ID());

        if (!$produto) continue;

        $preco_base = $produto->get_regular_price();
        $preco_extra = get_post_meta(get_the_ID(), '_preco_extra', true) ?: 10;
        $valor_seguro = get_post_meta(get_the_ID(), '_valor_seguro', true) ?: 1;
        $acessorios = get_post_meta(get_the_ID(), '_acessorios_produto', true);
        $flat_rates = get_post_meta(get_the_ID(), '_flat_rates', true);
        $flat_rates = is_array($flat_rates) ? $flat_rates : [];

        $titulo = $produto->get_name();
        $descricao_completa = $produto->get_description();
        $resumo = $produto->get_short_description();
        $imagem_id = $produto->get_image_id();
        $imagem_html = wp_get_attachment_image($imagem_id, 'medium', false, ['class' => 'rounded shadow']);
    ?>
    <style>
        #rentalForm {
            background: #ffffff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.08);
            font-family: 'Montserrat', sans-serif;
            max-width: 700px;
            margin: auto;
        }

        #rentalForm h2, #rentalForm h3 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }

        #rentalForm label {
            display: block;
            margin-top: 20px;
            margin-bottom: 6px;
            font-weight: 500;
            color: #444;
        }

        #rentalForm input[type="text"],
        #rentalForm input[type="date"],
        #rentalForm input[type="time"],
        #rentalForm select {
            width: 100%;
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
            box-sizing: border-box;
        }

        #rentalForm input[type="radio"],
        #rentalForm input[type="checkbox"] {
            margin-right: 8px;
        }

        #rentalForm div {
            margin-bottom: 12px;
        }

        #rentalForm button {
            margin-top: 25px;
            width: 100%;
            padding: 14px;
            background-color: #ff6a00;
            border: none;
            color: white;
            font-size: 18px;
            border-radius: 12px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        #rentalForm button:hover {
            background-color: #e25d00;
        }

        #acessoriosBox label {
            display: block;
            margin-bottom: 5px;
        }

        #rentalForm input::placeholder {
            color: #999;
        }

        .form-section-title {
            margin-top: 2em;
            margin-bottom: 1em;
            font-size: 1.2em;
            color: #ff6a00;
            text-align: left;
            font-weight: bold;
        }

        .form-tip {
            font-size: 0.95em;
            color: #888;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            #rentalForm {
                padding: 20px;
            }
        }
    </style>

    <section data-bs-version="5.1" class="content10 XKn" id="content10-2w" style="margin-bottom: 3em;">

    <div class="container">


                <div class="item features-image active" style="margin-top:2em;">
                    <a href="/products">Back to product page</a>
                    <center>
                        <h1><?php echo esc_html($titulo); ?></h1>
                        <div class="item-wrapper">


                            <div class="item-img">
                                <div class="imagem-produto item-img"><?php echo $imagem_html; ?></div>
                            </div>
                            <div class="wrap-description">
                                <h2>Description:</h2>
                                <div><?php echo wpautop($descricao_completa); ?></div>
                            </div>
                            <div class="wrap-short-description" >
                                <p>Summary</p>
                                <div><?php echo wpautop($resumo);?></div>
                            </div>
                        </div>
                    </center>

        </div>
        <div class="content-wrapper text-white text-center py-5">
            <div class="product-prices mbr-fonts-style lead" style="color: #333 !important;">
                <?php if (!empty($flat_rates)) : ?>
                    <?php foreach ($flat_rates as $item) : ?>
                        <p>💰 Flat rate 1 to <?php echo esc_html($item['dias']); ?> days: <strong>US$ <?php echo number_format($item['preco'], 2); ?></strong></p>
                        
                    <?php endforeach; ?>
                    <p>➕ Extra price (per additional day): <strong>US$ <?php echo number_format($preco_extra, 2); ?></strong></p>
                <?php else: ?>
                    <p>💰 Flat Rate (1 to 3 days): <strong>US$ <?php echo number_format($preco_base, 2); ?></strong></p>
                    <p>⏱️  Price for 4 days:  <strong>US$ <?php echo number_format($preco_base + $preco_extra, 2); ?></strong></p>
                    <p>⏱️  Price for 5 days:  <strong>US$ <?php echo number_format($preco_base + ($preco_extra * 2), 2); ?></strong></p>
                    <p>⏱️  Price for 6 days:  <strong>US$ <?php echo number_format($preco_base + ($preco_extra * 3), 2); ?></strong></p>
                    <p>⏱️  Price for 7 days:  <strong>US$ <?php echo number_format($preco_base + ($preco_extra * 4), 2); ?></strong></p>
                    <p>➕ Extra price (per additional day): <strong>US$ <?php echo number_format($preco_extra, 2); ?></strong></p>
                <?php endif; ?>
                <p>🛡️ Optional insurance: <strong>US$ <?php echo number_format($valor_seguro, 2); ?></strong></p>
            </div>
        </div>

    </section>

    <div class="container produto-container"
         data-produto-id="<?php the_ID(); ?>"
         data-preco-base="<?php echo esc_attr($preco_base); ?>"
         data-preco-extra="<?php echo esc_attr($preco_extra); ?>"
         data-flat-rates='<?php echo json_encode($flat_rates); ?>'>

        <form id="rentalForm">
            <div class="form-section-title">Rental Details</div>
            <div class="form-tip">Please fill in all fields to ensure a smooth rental experience.</div>

            <label>Delivery location:</label><br>
            <input type="radio" name="local_entrega" value="resort" checked> Choose Resort
            <input type="radio" name="local_entrega" value="especifico"> Specific Location

            <div id="selectResortBoxEntrega">
                <label for="localEntrega">Select the Resort:</label>
                <select id="localEntrega" name="localEntrega">
                <option value="">Select a location</option>
                <?php
                    $locais = get_option('locais_retirada');
                    $lista = explode("\n", $locais);
                    foreach ($lista as $local) {
                        $local = trim($local);
                        if ($local) {
                            echo "<option class='skiptranslate' value='{$local}'>{$local}</option>";
                        }
                    }
                ?>
                </select>
            </div>

            <div id="inputEspecificoBoxEntrega" style="display: none;">
                <label for="localEspecificoEntrega">Enter the location:</label>
                <input type="text" id="localEspecificoEntrega" name="localEspecificoEntrega" placeholder="Address or hotel name">
            </div>

            <label>Delivery date:</label>
            <input type="date" id="dataEntrega" required>

            <label>Delivery time (Only 08:00 to 20:00):</label>
            <input type="time" id="horaEntrega" required min="08:00" max="20:00">

            <div class="form-section-title">Return Details</div>

            <label>Return location:</label><br>
            <input type="radio" name="local_retorno" value="resort" checked> Choose Resort
            <input type="radio" name="local_retorno" value="especifico"> Specific Location

            <div id="selectResortBoxRetorno">
                <label for="localRetorno">Select the Resort:</label>
                <select id="localRetorno" name="localRetorno">
                <option value="">Select a location</option>
                <?php
                    $locais = get_option('locais_entrega');
                    $lista = explode("\n", $locais);
                    foreach ($lista as $local) {
                        $local = trim($local);
                        if ($local) {
                            echo "<option class='skiptranslate' value='{$local}'>{$local}</option>";
                        }
                    }
                ?>
                </select>
            </div>

            <div id="inputEspecificoBoxRetorno" style="display: none;">
                <label for="localEspecificoRetorno">Enter the location:</label>
                <input type="text" id="localEspecificoRetorno" name="localEspecificoRetorno" placeholder="Address or hotel name">
            </div>

            <label>Return date:</label>
            <input type="date" id="dataRetorno" required min="">
            <script>
                // Atualiza o min do dataRetorno quando dataEntrega muda
                document.addEventListener('DOMContentLoaded', function() {
                    const dataEntrega = document.getElementById('dataEntrega');
                    const dataRetorno = document.getElementById('dataRetorno');
                    dataEntrega.addEventListener('change', function() {
                        dataRetorno.value = '';
                        dataRetorno.min = this.value;
                    });
                });
            </script>

            <label>Return time (Only 08:00 to 20:00):</label>
            <input type="time" id="horaRetorno" required min="08:00" max="20:00">

            <div class="form-section-title">Family Information</div>
            <label>Enter the family surname:</label>
            <input type="text" id="sobrenome" placeholder="e.g. Johnson Family" required>

            <?php if (!empty($acessorios)) {
                echo '<div class="form-section-title">Accessories</div>';
                echo '<div class="form-tip">Select any accessories you wish to add to your rental.</div>';
                echo '<div id="acessoriosBox">';
                foreach ($acessorios as $i => $item) {
                    $nome = esc_html($item['nome']);
                    $valor = number_format($item['valor']);
                    echo "<label><input type='checkbox' class='acessorioCheck' data-valor='{$valor}' value='{$nome}'> {$nome} (+US$ {$valor})</label><br>";
                }
                echo '</div>';
            }
            ?>

            <div class="form-section-title">Insurance</div>
            <label>Would you like to add insurance?</label>
            <input type="radio" name="seguro" value=<?php echo esc_attr($valor_seguro); ?> id="seguroSim"> Yes, I accept (US$ <?php echo esc_attr($valor_seguro); ?>)
            <input type="radio" name="seguro" value="nao" checked> No, I decline insurance

            <h5 class="totalStyle">
                <span>Total (without TAX): <span id="totalSemTaxa"><strong>US$ 0.00</strong></span></span><br><br>
                <span>Taxes (6,5%): <span id="taxesValue"><strong>US$ 0.00</strong></span></span><br><br>
                <span>Total + 6,5% TAX: <span id="totalPrice"><strong>US$ 0.00</strong></span></span>
            </h5>
            <!-- Modal para os Termos e Condições -->
            <div id="termsModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000;">
                <div style="background: #fff; margin: 10% auto; padding: 20px; width: 80%; max-width: 600px; border-radius: 8px; position: relative;">
                    <h2>Terms & Conditions</h2> 
                    <div style="overflow-y: auto; max-height: 400px; margin-bottom: 20px;">
                        <h3>Terms &amp; Conditions</h3>
                        <h4>Rental Agreement Terms &amp; Conditions</h4>
                        <p>
                            We are honest with our customers and provide the following information up front prior to your rental, rather than having it appear as a shock later should such an event occur.
                        </p>
                        <hr>
                        <h5>Top Rentals Scooters. Rental Agreement</h5>
                        <p>
                            Top Rentals Scooters. rents to the person signing this agreement (further known herein as “Renter”) for a mobility scooter/electric wheelchair/stroller and included accessories (further known herein as “Equipment”) and is subject to all the terms and conditions set forth in this Rental Agreement. The Renter agrees: Equipment is the sole property of Top Rentals Scooters. and is in good working condition when received. Renter will return the Equipment in the same condition as when received at the end of the rental period for inspection and battery recharge (if necessary), or sooner, upon demand by Top Rentals Scooters.
                        </p>
                        <h5>Personal Property</h5>
                        <p>
                            Top Rentals Scooters. shall not be liable or responsible for the loss of or damage to any property left, lost, damaged, stolen, stored or transported by Renter, its agents, servants, or employees, or any other person using the Equipment, either before or after the return thereof. Renter assumes all risk of such loss or damage and waives all claims against Top Rentals Scooters. by reason thereof. Renter agrees to hold Top Rentals Scooters. harmless from and to defend and indemnify Top Rentals Scooters. against all claims based upon or arising out of such loss or damage. Renter assumes all risk and liability for any loss, damage or injury, including death, to persons or property of Renter or others arising out of the use of the Equipment.
                        </p>
                        <h5>Equipment Condition</h5>
                        <p>
                            Renter affirms that Top Rentals Scooters. provides the Equipment in good operating condition and that the Equipment is clean and free of damage. Renter is responsible for the Equipment and any included accessories and will reimburse Top Rentals Scooters. for the full cost of replacement upon demand for any damage, loss, theft, or destruction of the Equipment and any included accessories. Renter understands and authorizes that Top Rentals Scooters. will document any repair or replacement costs of the Equipment and any included accessories. Top Rentals Scooters. will arrange for the repair or replacement of any defective equipment. Any exchanges or replacement of equipment that is deemed non-defective will incur a fee of $25.
                        </p>
                        <h5>Operation of Equipment</h5>
                        <ul>
                            <li>Renter shall require drivers to operate the Equipment with reasonable care and diligence and comply with any polices and ordinances in the locations where it is used along with the terms of this agreement.</li>
                            <li>Under no circumstances shall the Equipment be used or operated by any other person under 18 years of age.</li>
                            <li>No 2nd or tandem riders of any kind.</li>
                            <li>No phone calls or texting while in operation.</li>
                            <li>Renter shall defend, indemnify and hold harmless Top Rentals Scooters., all of their agents, officers, servants, and employees from and against any and all losses, liability claims, damages, injuries, demands, actions and causes of action whatsoever, arising out of or related to any loss, damage or injury claimed by persons that may arise from the use, operation or driving of the Equipment, provided that such loss or damage was not caused by the fault or gross negligence and willful misconduct of Top Rentals Scooters. or its employees.</li>
                            <li>Renter assumes all costs and expenses of every kind and nature, including legal fees and disbursements arising out of and in connection with the use, operation or driving of the Equipment.</li>
                            <li>Top Rentals Scooters. assumes no liability or responsibility for any acts or omissions of Renter or of Renter’s agents, servants, or employees.</li>
                        </ul>
                        <h5>Damage, Loss, Theft and Reporting</h5>
                        <p>
                            Renter shall notify Top Rentals Scooters. within two hours of any and all extreme or unnatural accidents and damage resulting from the neglectful use or dangerous operation of the Equipment. Renter agrees to pay all costs, expenses, and attorneys fees incurred by Top Rentals Scooters. in collecting sums due or in regaining possession of Equipment or in enforcing or recovering any damage, losses or claims against Renter in extreme circumstantial occurrences. The Renter agrees to inform Top Rentals Scooters. immediately of any defect or malfunction while in use. Once notified, Top Rentals Scooters. will make every effort to remedy the issue in a timely manner. The Renter agrees to return any and all rental item(s) and accessories, charging apparatus, decals, or components, in the same condition as they was delivered, and to report any loss, theft, damage or destruction to the rental item(s) immediately to Top Rentals Scooters.
                        </p>
                        <h5>Representation</h5>
                        <p>
                            Renter or driver of the Equipment shall in no event be deemed the agent or employee of Top Rentals Scooters. in any manner or for any purpose whatsoever. Any individual executing this Agreement as Renter in a representative capacity shall be bound personally, jointly and severally, with such fiduciary, corporation or other entity as to all obligations, expressed or implied, arising hereunder. If any provisions hereof or the application of any provisions to any person or circumstance is held invalid or unenforceable, the remainder hereof and the application of such provision to other persons or circumstances shall remain valid and enforceable.
                        </p>
                        <h5>Payment Policies</h5>
                        <ul>
                            <li>Top Rentals Scooters. accepts major credit cards (MasterCard, Visa, Discover Card, and American Express) for credit identification and payment at the time of rental.</li>
                            <li>GMS upon delivery may require identification such as a valid passport, valid driver's license or a valid state issued (non-driver’s) identification card with picture to verify accurate delivery.</li>
                            <li>Prepaid Debit or Gift cards are not acceptable methods of credit or identification. Checks or Money Orders are not accepted.</li>
                        </ul>
                        <h5>Delivery and Pick-Up Appointments</h5>
                        <p>
                            Due to increased volume of business, Top Rentals Scooters. continues to deliver services at predetermined times at requested locations as set forth in the original order made by Renter. All times selected by Renter are officially observed as requests by Top Rentals Scooters. office staff and they will maintain these requests as closely as possible, however a final time - which may have been adjusted per availability - will be sent to the Renter for confirmation up to 48 hours prior of delivery and pick-up date to maintain efficiency and communication. These times must be confirmed by Renter and adhered to or a rescheduled appointment will be required per availability. If for any reason, rental equipment is considered "left" or "abandoned" at a resort labelled IN-PERSON such as a Disney resort where we normally meet you for delivery and pickup without authorization from the resort's respective Bell Services Manager or Resort Manager AND their name provided to Gold Mobility Scooters LLC, It will incur a fee of $75 that will be due immediately.
                        </p>
                        <h5>Cancellation and No-Show Policy</h5>
                        <p>
                            As a courtesy to fellow customers (renters), kindly cancel any unneeded reservation if your plans change, and as soon as possible. Renter may cancel this order at any time prior to scheduled delivery day and receive a 100% refund to the original payment method, or they can request a credit for a future reservation with no expiration. All refunds will be refunded only to the EXACT credit card the original charge was processed on. In the case of a closed account or canceled debit, credit card, or gift card a future rental credit voucher will be issued. All deliveries and pick-ups coordinated by office staff will be completed per issued time and location; a 10 minute "grace period" window will exist beyond the scheduled time with no exceptions in order for drivers to maintain a scheduled list of appointments. In the event that Top Rentals Scooters. attempts delivery or pick-up of Equipment at the established appointment time and location, and the Renter is not available and has not communicated any changes or delays to office staff or driver, an appointment reschedule will be required and coordinated by the office per availability. If Top Rentals Scooters. office staff or drivers cannot contact the Renter and the appointment has been missed, the appointment will be on hold with potential cancellation until contact can be made to confirm a new time based on availability.
                        </p>
                        <h5>Rental Extensions and Equipment Return</h5>
                        <p>
                            Equipment and any included accessories are due back at the date/time specified on the rental agreement so the Equipment can be picked up. If you need to extend your rental, you must call prior to expiration date and time to determine if the rental can be extended. Top Rentals Scooters. may, at its own discretion allow or deny any extension. Top Rentals Scooters. reserves the right to charge the Renter’s card and Renter agrees to allow or pay the charge of $15 for each additional day past the standard 4-7 day rate or fraction of a day the Equipment is kept by the Renter beyond the prearranged return date and time on the rental contract. Renter may request a change of product for reasons of size or transportation issues and agree upon the set fee for exchanges; the approval of the request is at the sole discretion of Top Rentals Scooters. and will be based on the availability of equipment and delivery personnel. The Renter is responsible to pay the replacement cost of any lost parts or accessories, whether paid for or provided free of charge. These include but are not limited to: oxygen bottle holders, sunshades, baskets, cane/walker/crutch holders, phone holders, straps, harnesses, stroller consoles, rain covers, coolers, bedding materials, trays, storage bags and any other accessories or functional material provided. These items may be provided for a small fee or free of charge during the rental period but are in no way guaranteed to be available during the rental.
                        </p>
                        <h5>Theft and Damage Insurance</h5>
                        <p>
                            Top Rentals Scooters. offers our Renter's a zero deductible, free theft and accidental damage waiver insurance policy with every scooter rental. All insurance products or waivers will terminate, and are void in their entirety if the Equipment and any included accessories are not returned by the designated date and time on the original rental contract.
                        </p>
                        <h5>Dead Battery, Key Loss or Re-Delivery</h5>
                        <p>
                            The Equipment (scooters or electric wheelchairs) is fully charged at time of delivery; Renter is responsible to charge the battery every night during the rental period inside their hotel room or rental property. We maintain the Equipment be plugged in overnight whether the power meter shows a discharge or not. The unit is designed to charge automatically, however do not keep the Equipment charger plugged in more than twenty four (24) hours, as damage to the battery can occur. If charging overnight is neglected and the unit lacks sufficient power, Renter assumes the responsibility of recharging the unit to a productive level. Do not charge Equipment for any length of time less than 60 minutes (1 hour) as this may result in a de-calibration of the battery meter and produce an inaccurate readout. Renter loses the key during rental a $15 fee for key replacement fee is charged.
                        </p>
                        <p>
                            Printed manual is available upon request at time of rental. It provides safety and operational instructions to safely use / operate the Equipment.
                        </p>
                    </div>
                    <label>
                        <input type="checkbox" id="acceptTerms"> I have read and agree to the Terms & Conditions
                    </label>
                    <button id="closeModal" style="margin-top: 20px; padding: 10px 20px; background: #ff6a00; color: #fff; border: none; border-radius: 5px; cursor: pointer;">Close</button>
                </div>
            </div>

            <!-- Botão para abrir a modal -->
            <div class="form-section-title">Terms & Conditions</div>
            <div class="form-tip">
                <p>
                    To add to cart you must access the link, read and agree to the ->
                    <a href="#" id="openModal" style="text-decoration: underline;">Rental Agreement Terms & Conditions</a>.
                </p>
            </div>

            
            <!-- Botão de envio -->
            <button type="submit" id="submitButton" disabled>Add to Cart</button>

            <script>
                // Abrir a modal
                document.getElementById('openModal').addEventListener('click', function(event) {
                    event.preventDefault();
                    document.getElementById('termsModal').style.display = 'block';
                });

                // Fechar a modal
                document.getElementById('closeModal').addEventListener('click', function(event) {
                    event.preventDefault();
                    document.getElementById('termsModal').style.display = 'none';
                });

                // Habilitar o botão de envio após aceitar os termos
                document.getElementById('acceptTerms').addEventListener('change', function(event) {
                    event.preventDefault();
                    const submitButton = document.getElementById('submitButton');
                    submitButton.disabled = !this.checked;
                });
            </script>
        </form>
    </div>

</section>

<?php endwhile; ?>


<?php get_footer(); ?>